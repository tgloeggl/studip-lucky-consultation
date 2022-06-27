<?php
/**
 * LuckyConsultation.class.php
 */

use LuckyConsultation\AppFactory;
use LuckyConsultation\RouteMap;

class LuckyConsultation extends StudipPlugin implements StandardPlugin
{
    const GETTEXT_DOMAIN = 'lucky-consultation';

    private $assetsUrl;

    /**
     * Initialize a new instance of the plugin.
     */
    public function __construct()
    {
        parent::__construct();

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
            PluginEngine::getURL($this, [], 'course/index/false')
        );
        $navigation->setBadgeNumber(0);
        $navigation->setDescription("Losbasierte Sprechstundenvergabe");
        /*
        if ($ocgetcount > 0) {
            $navigation->setImage(
                Icon::create($this->getPluginURL() . '/images/opencast-red.svg',
                    Icon::ROLE_ATTENTION,
                    ['title' => 'Opencast']
                ));
        } else {
            $navigation->setImage(
                Icon::create($this->getPluginURL() . '/images/opencast-grey.svg',
                    Icon::ROLE_INACTIVE,
                    ['title' => 'Opencast']
                ));
        }
        */

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

        //$ocmodel = new OCCourseModel($course_id);
        $title   = 'Losbasierte Sprechstunden';
        /*
        if ($ocmodel->getSeriesVisibility() == 'invisible') {
            $title .= " (". $this->_('versteckt'). ")";
        }
        */

        $main    = new Navigation($title);

        $main->setURL(PluginEngine::getURL($this, [], 'course/index'));

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

        $metadata['description'] = $this->_("TODO");

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

        return true;
    }

    public function cleanCourse($event, $course)
    {
        //TODO
        /*
        $course_id = $course->getId();
        OCScheduledRecordings::deleteBySQL('seminar_id = ?', [$course_id]);
        OCSeminarEpisodes::deleteBySQL('seminar_id = ?', [$course_id]);
        OCSeminarSeries::deleteBySQL('seminar_id = ?', [$course_id]);
        OCSeminarWorkflowConfiguration::deleteBySQL('seminar_id = ?', [$course_id]);
        OCTos::deleteBySQL('seminar_id = ?', [$course_id]);

        if ($course_link = OCUploadStudygroup::findOneBySQL('course_id = ?', [$course_id])) {
            $studygroup_id = $course_link['studygroup_id'];
            $course_link->delete();
            Course::find($studygroup_id)->delete();
        }
        else if ($studygroup_link = OCUploadStudygroup::findOneBySQL('studygroup_id = ?', [$course_id])) {
            $studygroup_link->delete();
        }
        */
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
