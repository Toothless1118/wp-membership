<?php include('style.inc.php'); ?>

<ul id="<?php echo $data['id'] ?>" class="social-networking social-networking-style-2">

    <?php

    $socialNetworkName = '';
    $newTab = '';
    $hideText = $data['hide_text'];

    if($data['new_tab'] === 'Y') {
        $newTab = '_blank';
    }

    foreach($data as $key => $value) {
        if(strpos($key, 'url') && !empty($value)) {
            $temp = explode("_", $key);
            if($hideText !== 'Y') {
                $socialNetworkName = $temp[0];
            }


            $path = OPPP_BASE_DIR . "css/elements/images/op_social_networking_icons/style-2/" . $temp[0] . '.svg';
            $svg = file_get_contents($path);

            echo '<li class="' . $key . '">
                    <a href="' . $value . '" target="' . $newTab . '">
                        <div class="box">
                            ' . $svg . '
                            <p>' . $socialNetworkName . '</p>
                        </div>
                    </a>
                </li>';
        }
    }
    ?>
</ul>
