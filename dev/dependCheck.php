<?php
    $composerInstalledFiles = file_get_contents(dirname(__FILE__) . '/../composer.lock');
    $composerInstalledFiles = json_decode($composerInstalledFiles, true);

    foreach ($composerInstalledFiles['packages'] as $package) {
        $feedVersion = getFeed('https://packagist.org/feeds/package.' . $package['name'] . '.rss');

        echo $package['name'];
        if ($package['version'] == $feedVersion) {
            echo "\t\t[OKAY]\n";
        } else {
            echo "\t\t[UPDATE] -> " . $feedVersion . "\n";
        }
    }

    /**
     * @param $feed_url
     *
     * @return mixed
     */
    function getFeed($feed_url)
    {

        $content = file_get_contents($feed_url);
        $x       = new SimpleXmlElement($content);

        return preg_replace('/.* \(([\w\d.\-]+)\)/im', '$1', $x->channel->item[0]->title);
    }