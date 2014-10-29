<!DOCTYPE html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"><!--<![endif]-->

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Block Host</title>
    <link rel="stylesheet" href="" media="screen" type="text/css" />
    <script src="../css/" type="text/javascript"></script>
</head>

<body>
{% block bodyContent %}
{{ bodyContent }}
{% endblock %}

<p>asdf
{% include 'block-module-pages/pageModule.volt' %}
</p>

<p>
{{ message }}
</p>
</body>