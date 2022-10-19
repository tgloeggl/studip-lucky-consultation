<?php
/**
 * LuckyConsultation.class.php
 */

use LuckyConsultation\AppFactory;
use LuckyConsultation\RouteMap;
use LuckyConsultation\Models\Dates;
use LuckyConsultation\Models\Pools;
use LuckyConsultation\Models\WaitingList;

class LuckyConsultation extends StudipPlugin implements StandardPlugin, PrivacyPlugin, SystemPlugin
{
    const GETTEXT_DOMAIN = 'lucky-consultation';

    private $assetsUrl;

    /**
     * Initialize a new instance of the plugin.
     */
    public function __construct()
    {
        parent::__construct();

        NotificationCenter::addObserver($this, 'userDidDelete', 'UserDidDelete');
        NotificationCenter::addObserver($this, 'courseDidDelete', 'CourseDidDelete');
        NotificationCenter::addObserver($this, 'userDidLeaveCourse', 'UserDidLeaveCourse');
        NotificationCenter::addObserver($this, 'userDidMigrate', 'UserDidMigrate');

        bindtextdomain(static::GETTEXT_DOMAIN, $this->getPluginPath() . '/locale');
        bind_textdomain_codeset(static::GETTEXT_DOMAIN, 'UTF-8');
        $this->assetsUrl = rtrim($this->getPluginURL(), '/').'/assets';
    }

    /**
     * Plugin localization for a single string.
     * This method supports sprintf()-like execution if you pass additional
     * parameters.
     *
     * @param String $string String to translate
     * @return translated string
     */
    public function _($string)
    {
        $result = static::GETTEXT_DOMAIN === null
            ? $string
            : dcgettext(static::GETTEXT_DOMAIN, $string, LC_MESSAGES);
        if ($result === $string) {
            $result = _($string);
        }

        if (func_num_args() > 1) {
            $arguments = array_slice(func_get_args(), 1);
            $result    = vsprintf($result, $arguments);
        }

        return $result;
    }

    /**
     * Plugin localization for plural strings.
     * This method supports sprintf()-like execution if you pass additional
     * parameters.
     *
     * @param String $string0 String to translate (singular)
     * @param String $string1 String to translate (plural)
     * @param mixed $n Quantity factor (may be an array or array-like)
     * @return translated string
     */
    public function _n($string0, $string1, $n)
    {
        if (is_array($n)) {
            $n = count($n);
        }

        $result = static::GETTEXT_DOMAIN === null
            ? $string0
            : dngettext(static::GETTEXT_DOMAIN, $string0, $string1, $n);
        if ($result === $string0 || $result === $string1) {
            $result = ngettext($string0, $string1, $n);
        }

        if (func_num_args() > 3) {
            $arguments = array_slice(func_get_args(), 3);
            $result    = vsprintf($result, $arguments);
        }

        return $result;
    }

    /**
     * This method takes care of the Navigation
     *
     * @param string   course_id
     * @param string   last_visit
     */
    public function getIconNavigation($course_id, $last_visit, $user_id = null)
    {
        if (!$this->isActivated($course_id)) {
            return;
        }

        $navigation = new Navigation(
            'luckyconsultation',
            PluginEngine::getURL($this, [], 'course/index')
        );
        $navigation->setBadgeNumber(0);
        $navigation->setDescription("Losbasierte Sprechstundenvergabe");

        return $navigation;
    }

    /**
     * Return a template (an instance of the Flexi_Template class)
     * to be rendered on the course summary page. Return NULL to
     * render nothing for this plugin.
     *
     * The template will automatically get a standard layout, which
     * can be configured via attributes set on the template:
     *
     *  title        title to display, defaulAppFactoryts to plugin name
     *  icon_url     icon for this plugin (if any)
     *  admin_url    admin link for this plugin (if any)
     *  admin_title  title for admin link (default: Administration)
     *
     * @return object   template object to render or NULL
     */
    public function getInfoTemplate($course_id)
    {
        return null;
    }

    public function getTabNavigation($course_id)
    {
        if (!$this->isActivated($course_id)) {
            return;
        }

        $title   = 'Losbasierte Sprechstunden';
        $main    = new Navigation($title);

        if ($GLOBALS['perm']->have_studip_perm('tutor', $course_id)) {
            $main->setURL(PluginEngine::getURL($this, [], 'course/index/#/editor'));
        } else {
            $main->setURL(PluginEngine::getURL($this, [], 'course/index'));
        }

        $index = new Navigation($this->_('Sprechstunden'));
        $index->setURL(PluginEngine::getURL($this, [], 'course/index'));
        $main->addSubNavigation('index', $index);

        return array('luckyconsulation' => $main);
    }

    /**
     * return a list of ContentElement-objects, containing
     * everything new in this module
     *
     * @param string $course_id the course-id to get the new stuff for
     * @param int $last_visit when was the last time the user visited this module
     * @param string $user_id the user to get the notification-objects for
     *
     * @return array an array of ContentElement-objects
     */
    public function getNotificationObjects($course_id, $since, $user_id)
    {
        return false;
    }

    /**
     * @inherits
     *
     * Overwrite default metadata-function to return correctly encoded strings
     * depending on Stud.IP version
     *
     * @return array correctly encoded metadata
     */
    public function getMetadata()
    {
        $metadata = parent::getMetadata();

        $metadata['pluginname'] = $this->_("Losbasierte Sprechstundenvergabe");
        $metadata['displayname'] = $this->_("Losbasierte Sprechstundenvergabe");

        $metadata['description'] = $this->_('Anhand von frei konfigurierbaren Terminen und zusätzlicher möglicher Aufteilung in verschiedene Lospools '
            .'können sich bei diesem Tool Studierende auf die Warteliste dieser Termine setzen lassen. Es wird dann zu einstellbaren Zeitpunkten '
            . 'ausgelost, wer den jeweiligen Termin bekommt.');

        $metadata['summary'] = $this->_("Losbasierte Sprechstundenvergabe");

        return $metadata;
    }

    /**
     * Return the name of this plugin.
     */
    public function getPluginName()
    {
        return 'Losbasierte Sprechstundenvergabe';
    }

    /**
     * Returns whether the plugin may be activated in a certain context.
     *
     * @param Range $context
     * @return bool
     */
    public function isActivatableForContext(Range $context)
    {

        if ($context->getRangeType() === 'course' &&
            $context->getSemClass()['studygroup_mode']) {
            return false;
        }
        if ($context->getRangeType() === 'institute') {
            return false;
        }

        // check, if course belongs to the correct institute
        // 08422bc3d757b8d2f63d97d306013f83 -  Klinische Psychologie und Psychotherapie
        if ($context->getRangeType() === 'course'
            && $context->home_institut->id == '08422bc3d757b8d2f63d97d306013f83'
        ) {
            return true;
        }

        return false;
    }

    /**
     * Export available data of a given user into a storage object
     * (an instance of the StoredUserData class) for that user.
     *
     * @param StoredUserData $storage object to store data into
     */
    public function exportUserData(StoredUserData $storage)
    {
        $field_data = DBManager::get()->fetchAll("SELECT * FROM luckyconsultation_dates WHERE user_id = ?", [$storage->user_id]);
        if ($field_data) {
            $storage->addTabularData(_('Losbasierte Sprechstundenvergabe - Einträge'), 'luckyconsultation_dates', $field_data);
        }

        $field_data = DBManager::get()->fetchAll("SELECT * FROM luckyconsultation_waitinglist WHERE user_id = ?", [$storage->user_id]);
        if ($field_data) {
            $storage->addTabularData(_('Losbasierte Sprechstundenvergabe - Einträge'), 'luckyconsultation_waitinglist', $field_data);
        }
    }

    public function userDidDelete($event, $user)
    {
        require_once __DIR__ . '/vendor/autoload.php';

        $db = DBManager::get();
        $db->execute('UPDATE luckyconsultation_dates SET user_id = NULL WHERE user_id = ?', [$user_id]);

        WaitingList::deleteBySQL('user_id = ?', [$user->id]);
    }

    public function courseDidDelete($event, $course)
    {
        require_once __DIR__ . '/vendor/autoload.php';

        Dates::deleteBySQL('course_id = ?', [$course->id]);
        Pools::deleteBySQL('course_id = ?', [$course->id]);
    }

    public function userDidLeaveCourse($event, $course_id, $user_id)
    {
        require_once __DIR__ . '/vendor/autoload.php';

        $db = DBManager::get();
        $db->execute('UPDATE luckyconsultation_dates SET user_id = NULL WHERE user_id = ?', [$user_id]);

        // find all pools for the course
        $pools = Pools::findByCourse_id($course_id);

        foreach ($pools as $pool) {
            foreach ($pool->dates as $date) {
                foreach ($date->waitinglist as $entry)
                if ($entry->user_id == $user_id) {
                    $entry->delete();
                }
            }
        }
    }

    public function userDidMigrate($event, $user_id, $new_id)
    {
        require_once __DIR__ . '/vendor/autoload.php';

        $db = DBManager::get();

        $db->execute("UPDATE luckyconsultation_dates       SET user_id = ? WHERE user_id = ?", [$new_id, $user_id]);
        $db->execute("UPDATE luckyconsultation_waitinglist SET user_id = ? WHERE user_id = ?", [$new_id, $user_id]);
    }

    /**
     * {@inheritdoc}
     */
    public function perform($unconsumed_path)
    {
        require_once __DIR__ . '/vendor/autoload.php';

        if (substr($unconsumed_path, 0, 3) == 'api') {
            $appFactory = new AppFactory();
            $app = $appFactory->makeApp($this);
            $app->group('/luckyconsultation/api', new RouteMap($app));
            $app->run();
        } else {
            $trails_root = $this->getPluginPath() . '/app';
            $dispatcher  = new Trails_Dispatcher($trails_root,
                rtrim(PluginEngine::getURL($this, null, ''), '/'),
                'index'
            );

            $dispatcher->current_plugin = $this;
            $dispatcher->dispatch($unconsumed_path);
        }
    }

    public function getAssetsUrl()
    {
        return $this->assetsUrl;
    }
}
