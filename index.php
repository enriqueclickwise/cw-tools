<html>
    <head>
    </head>
    <body>
    <h1>System Files</h1>
    <?php
        $directory = getcwd();
        $item = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
        while($item->valid()) {
            if (!$item->isDot()) {
                echo '<a href="'.$item->getSubPathName().'">'.$item->getSubPathName().'</a><br>';
            }
            $item->next();
        }
    ?>
    </body>
</html>