<?php

    $attributes = array('name' => 'btnboxform');
    $hidden = array('formname' => 'btnboxform');

    echo '<div id="btnbox">';

    echo form_open(uri_string(), $attributes, $hidden);

    if (isset($btnbox_content)) {

        unset($othtemp);
        $othtemp = '';

        unset($buttontemp);
        $buttontemp = '';

        foreach ($btnbox_content as $button) {

            $buttontemp .= '<div class="onebutton"';
            if (isset($button['title'])) {
                $buttontemp .= 'title="' . $button['title'] . '"';
            }

            $buttontemp .= '>';

            if (isset($button['content'])) {

                $buttontemp .= $button['content'];

            } else {

                $buttontemp .= '<a ';
                if (isset($button['id'])) {
                    $buttontemp .= 'id="' . $button['id'] . '">';
                }

                if (isset($button['buttonname'])) {
                    $buttontemp .= $button['buttonname'];
                }
                $buttontemp .= '</a>';

                if (isset($button['script'])) {
                    $othtemp .= $button['script'];
                }
            }
            $buttontemp .= '</div>';
        }
    }

    if ((isset($buttontemp)) && ($buttontemp <> NULL)) {
        echo $buttontemp;
    }

    echo form_close();
    echo $othtemp;
    echo '</div>';
