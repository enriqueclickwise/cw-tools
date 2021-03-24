<?php
    // Feed interface
    interface FeedInterface {
        // returns the information of the current feed
        function getFeedInfo();
        // returns all the information for current feed
        function getFeedData();
    }
?>