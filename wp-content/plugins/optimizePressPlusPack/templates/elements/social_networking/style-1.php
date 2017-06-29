<?php include('style.inc.php'); ?>

<ul id="<?php echo $data['id'] ?>" class="social-networking social-networking-style-1">
    <?php

    $newTab = '_self';
    if($data['new_tab'] === 'Y') {
        $newTab = '_blank';
    }

    foreach ($data as $key => $value) {
        if (strpos($key, 'url') && $value != '') {
            $temp = explode("_", $key);

            $path = OPPP_BASE_DIR . "css/elements/images/op_social_networking_icons/style-2/" . $temp[0] . '.svg';
            $svg = file_get_contents($path);

            echo '<li class="' . $key . '">
                    <a href="' . $value . '" target="' . $newTab . '">
                        <div class=box>
                            ' . $svg . '
                            <p>' . $temp[0] . '</p>
                        </div>
                    </a>
                </li>';
        }
    }
    ?>
</ul>
