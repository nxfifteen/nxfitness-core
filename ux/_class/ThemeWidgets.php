<?php

    class ThemeWidgets {

        /**
         * ThemeWidgets constructor.
         */
        public function __construct() { }

        /**
         * @param string $type
         * @param array  $input
         *
         * @return string
         */
        public function buildSnippet($type, $input) {
            $methodName = "build" . $type;
            if (method_exists($this, $methodName)) {
                return $this->$methodName($input);
            } else {
                return "Unknown snippet " . $type;
            }
        }

        /**
         * @param array  $input
         *
         * @return string
         */
        /** @noinspection PhpUnusedPrivateMethodInspection */
        private function buildWidgetNotification($input) {
            if (!array_key_exists("icon", $input)) $input['icon'] = "envelope-o";
            if (!array_key_exists("urgency", $input)) $input['urgency'] = "info";

            $returnHTML = "<div class=\"alert alert-" . $input['urgency'] . " clearfix\">";
            $returnHTML .= "<span class=\"alert-icon\"><i class=\"fa fa-" . $input['icon'] . "\"></i></span>";
            $returnHTML .= "<div class=\"notification-info\">";
            $returnHTML .= "<ul class=\"clearfix notification-meta\">";
            $returnHTML .= "<li class=\"pull-left notification-sender\">" . $input['sender'] . "</li>";
            $returnHTML .= "<li class=\"pull-right notification-time\">" . $input['time'] . "</li>";
            $returnHTML .= "</ul>";
            $returnHTML .= "<p>" . $input['msg'] . "</p>";
            $returnHTML .= "</div>";
            $returnHTML .= "</div>";
            return $returnHTML;
        }

        /**
         * @param array  $input
         *
         * @return string
         */
        /** @noinspection PhpUnusedPrivateMethodInspection */
        private function buildHeaderNotificationBar($input) {
            if (!array_key_exists("icon", $input)) $input['icon'] = "envelope-o";
	        if (!array_key_exists("urgency", $input)) $input['urgency'] = "info";
	        if (!array_key_exists("url", $input)) $input['url'] = "#";

            $returnHTML = "<li>";
            $returnHTML .= "<div class=\"alert alert-" . $input['urgency'] . " clearfix\">";
            $returnHTML .= "<span class=\"alert-icon\"><i class=\"fa fa-" . $input['icon'] . "\"></i></span>";
            $returnHTML .= "<div class=\"noti-info\">";
            $returnHTML .= "<a href=\"" . $input['url'] . "\">" . $input['msg'] . "</a>";
            $returnHTML .= "</div>";
            $returnHTML .= "</div>";
            $returnHTML .= "</li>";
            return $returnHTML;
        }

        /**
         * @param array  $input
         *
         * @return string
         */
        /** @noinspection PhpUnusedPrivateMethodInspection */
        private function buildHeaderInboxBar($input) {
	        if (!array_key_exists("url", $input)) $input['url'] = "#";

            $returnHTML = "<li>";
            $returnHTML .= "<a href=\"" . $input['url'] . "\">";
            $returnHTML .= "<span class=\"subject\">";
            $returnHTML .= "<span class=\"from\">" . $input['sender'] . "</span>";
            $returnHTML .= "<span class=\"time\">" . $input['time'] . "</span>";
            $returnHTML .= "</span>";
            $returnHTML .= "<span class=\"message\">";
            $returnHTML .= $input['msg'];
            $returnHTML .= "</span> </a>";
            $returnHTML .= "</li>";
            return $returnHTML;
        }

    }