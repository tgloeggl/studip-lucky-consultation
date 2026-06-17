<?php
/*
 * course.php - course controller
 */

class CourseController extends StudipController
{
    public function __construct($dispatcher)
    {
        parent::__construct($dispatcher);

        $this->plugin = $dispatcher->current_plugin;

        PageLayout::setHelpKeyword('LosbasierteSprechstundenvergabe');
    }

    /**
     * Common code for all actions: set default layout and page title.
     */
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);
        $this->course_id = Context::getId();
    }

    /**
     * This is the default action of this controller.
     */
    public function index_action()
    {
        Navigation::activateItem('/course/luckyconsulation/index');

        $this->setSidebar();
    }

        /**
     * Adds the content to sidebar.
     * @info: The rendered sidebar of this function gets deleted from DOM, because we are using CourseSidebar.vue,
     * therefore, we only need this to happen in php level!
     */
    private function setSidebar()
    {
        $sidebar = Sidebar::get();

        $actions = new \TemplateWidget(
            'Aktionen',
            $this->get_template_factory()->open('course/action_widget')
        );
        $sidebar->addWidget($actions);
    }
}
