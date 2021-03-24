<?php
    // FeedProduct interface
    interface FeedProduct {
        // returns an array of objects type Feed with all the feed info
        function transformCsv();
    }
?>