<h1>System Information</h1>
<h2>Host: <?=env('HOST_HOSTNAME')?></h2>
<h2>Build ID: <a href="https://code.cropcircle.io/Grownetics/Grownetics/builds/<?=$BUILD_ID?>" target="_blank"><?=$BUILD_ID?></a></h2>
<h3>Build Date: <?=$BUILD_DATE?></h3>
<h4>Service Links</h4>
<ul>
    <li><a href='http://<?=$this->request->env('HTTP_HOST')?>:3000/' target='_blank'>Grafana</a></li>
    <li><a href='http://<?=$this->request->env('HTTP_HOST')?>:15672/' target='_blank'>RabbitMQ</a></li>
    <li><a href='http://<?=$this->request->env('HTTP_HOST')?>:8500/' target='_blank'>Consul</a></li>
    <li><a href='http://<?=$this->request->env('HTTP_HOST')?>:8888/' target='_blank'>Chronograf</a></li>
</ul>

<div>
	<a href='#' class='toggleLink'>Time settings:</a>
	<pre class='toggleTarget' style='display: none;'><?php
    print_r(strtotime("00:00"));
    print_r(localtime());
    print_r(time());
    print_r(time()-strtotime("00:00"));
    print_r((time()-strtotime("00:00"))/60/60);
    print_r(date('l jS \of F Y h:i:s A'));
    print_r(gettimeofday());
    print_r(date("D, d M Y H:i:s"));
    print_r(date_default_timezone_get());
    print_r(date('l jS \of F Y h:i:s A'));
    ?></pre>
</div>


<div>
	<a href='#' class='toggleLink'>Feature Flags:</a>
	<pre class='toggleTarget' style='display: none;'><?php
		use Cake\Core\Configure;
	 print_r(Configure::read('FeatureFlags'));
	?></pre>
</div>
<div>
	<a href='#' class='toggleLink'>Server Environment Settings:</a>
	<pre class='toggleTarget' style='display: none;'><?=print_r($_SERVER)?></pre>
</div>
<div>
	<a href='#' class='toggleLink'>PHP Info:</a>
	<pre class='toggleTarget' style='display: none;'><?php
	function parse_phpinfo() {
    ob_start(); phpinfo(INFO_MODULES); $s = ob_get_contents(); ob_end_clean();
    $s = strip_tags($s, '<h2><th><td>');
    $s = preg_replace('/<th[^>]*>([^<]+)<\/th>/', '<info>\1</info>', $s);
    $s = preg_replace('/<td[^>]*>([^<]+)<\/td>/', '<info>\1</info>', $s);
    $t = preg_split('/(<h2[^>]*>[^<]+<\/h2>)/', $s, -1, PREG_SPLIT_DELIM_CAPTURE);
    $r = array(); $count = count($t);
    $p1 = '<info>([^<]+)<\/info>';
    $p2 = '/'.$p1.'\s*'.$p1.'\s*'.$p1.'/';
    $p3 = '/'.$p1.'\s*'.$p1.'/';
    for ($i = 1; $i < $count; $i++) {
        if (preg_match('/<h2[^>]*>([^<]+)<\/h2>/', $t[$i], $matchs)) {
            $name = trim($matchs[1]);
            $vals = explode("\n", $t[$i + 1]);
            foreach ($vals AS $val) {
                if (preg_match($p2, $val, $matchs)) { // 3cols
                    $r[$name][trim($matchs[1])] = array(trim($matchs[2]), trim($matchs[3]));
                } elseif (preg_match($p3, $val, $matchs)) { // 2cols
                    $r[$name][trim($matchs[1])] = trim($matchs[2]);
                }
            }
        }
    }
    return $r;
}
	print_r(parse_phpinfo())?></pre>
</div>
<div>
	<a href='#' class='toggleLink'>System Load:</a>
	<pre class='toggleTarget' style='display: none;'><?php print_r(sys_getloadavg())?></pre>
</div>
