<?php

class BreadcrumbFormatter
{
	public static function renderBreadcrumbTrail($breadcrumbArray)
	{
    $result = '<breadcrumb>';

    $n = 0;
    foreach($breadcrumbArray as $name=>$link)
    {
      if($n > 0) $result .= ' / ';

      if($link)
      {
        $result .= sprintf('<a href="%s">%s</a>', $link, $name);
      }
      else
      {
        $result .= $name;
      }
      $n++;
    }
    $result .= '</breadcrumb>';

    return $result;
  }
}
