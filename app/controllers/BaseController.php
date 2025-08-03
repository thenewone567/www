<?php
/**
 * BaseController for managed layout rendering
 * Extends core Controller and provides renderLayout()
 */
class BaseController extends Controller
{
    /**
     * Render a view with managed layout (header, sidebar, footer)
     * @param string $view View path relative to app/views/
     * @param array $data Data to pass to the view
     * @param bool $useLayout Whether to use the full layout (default: true)
     */
    public function renderLayout($view, $data = [], $useLayout = true)
    {
        if ($useLayout) {
            require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php';
        }
        $this->view($view, $data);
        if ($useLayout) {
            require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php';
        }
    }
}
