<?php
namespace Acelaya\SlimCli\Controller;

use SlimController\SlimController;

class AbstractControleController extends SlimController
{
    /**
     * @param $flagName
     * @return bool
     */
    protected function getCliFlag($flagName)
    {
        $pathinfo = $this->app->environment()->offsetGet('PATH_INFO') . ' ';

        // The flag must have no value, so it should be at the end opf the path or have an space after it.
        // It also should have an space before it
        $pos = strpos($pathinfo, ' --' . $flagName . ' ');
        return $pos !== false;
    }

    /**
     * @param $paramName
     * @param null $default
     * @return mixed
     */
    protected function getCliParam($paramName, $default = null)
    {
        $params = $this->app->router()->getCurrentRoute()->getParams();
        return isset($params[$paramName]) && ! empty($params[$paramName]) ? $params[$paramName] : $default;
    }
}
