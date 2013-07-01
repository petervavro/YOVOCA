<?php

    /* GET OBJECT TO LAYOUT */

        if (!isset($Article['nowrapper'])) {

            echo '<div class="' . (isset($Article['divclass']) ? $Article['divclass'] : 'textPlace') . '"' . (isset($Article['divid']) ? ' id="' . $Article['divid'] . '"' : null ) . '>';
        }

        if (isset($Article['Title'])) {

            echo '<h3>';

            if (isset($Article['aON'])) {
                echo '<a href="#">';
            }

            echo $Article['Title'];

            if (isset($Article['aON'])) {
                echo '</a>';
            }

            echo '</h3>';
        }

        echo '<div class="wrapText">' . (isset($Article['ArticleText']) ? $Article['ArticleText'] : null) . '</div>';

        if (!isset($Article['nowrapper'])) {

            echo '</div>';
        }
