<?php
require_once("/libs/mustache.php");
$base='{{! base.mustache }}
<html>
  <head>
    <title>{{$ title }}My Site{{/ title }}</title>
  </head>
  <h1>{{$ title }}My Site{{/ title }}</h1>
</html>';
$page='{{! page.mustache }}
{{< base }}
  {{$ title }}{{ page.title }} | My Site{{/ title }}
{{/ base }}';
$article='{{! article.mustache }}
{{< page }}
  {{$ title }}{{ article.title }} | My Site{{/ title }}
{{/ page }}';

$m= new Mustache_Engine([
    'pragmas' => [Mustache_Engine::PRAGMA_BLOCKS],
    'partials' => ['base'=>$base, 'page'=>$page]
]);

$m->render($article,[
    'article' => [
        'title' => 'Article Title!',
    ],
    'page' => [
        'title' => 'Page Title!',
    ],
]);

$base='{{! base.mustache }}
<!DOCTYPE html>
<html>
<head>...</head>
<body>
    {{$content}}{{/content}}
</body>
</html>';
$basic='{{! basic.mustache }}
<div class="basic-block">
    {{$content}}{{/content}}
</div>';
$mypage='{{! mypage.mustache }}
{{< base }}
    {{$content}}

        {{< basic-block }}
            {{$content}}
                Hello :-)
            {{/content}}
        {{/ basic-block }}

    {{/content}}
{{/ base }}';

$m= new Mustache_Engine([
    'pragmas' => [Mustache_Engine::PRAGMA_BLOCKS],
    'partials' => ['base'=>$base, 'basic-block'=>$basic]
]);

echo $m->render($mypage);