{**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *}
{foreach $stylesheets.external as $stylesheet}
  <link rel="stylesheet" href="{$stylesheet.uri}" type="text/css" media="{$stylesheet.media}">
{/foreach}

  <link rel="stylesheet" href="/themes/hummingbird/assets/css/custom.css?lo=12" type="text/css" media="all">


{foreach $stylesheets.inline as $stylesheet}
  <style>
    {$stylesheet.content}
  </style>
{/foreach}
