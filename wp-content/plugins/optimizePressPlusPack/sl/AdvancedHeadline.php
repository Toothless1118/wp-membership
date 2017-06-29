<?php

namespace OpSl\Element;

use OpSl\Util\Element as Util;

class AdvancedHeadline implements ElementInterface
{
    protected $atts;

    public function __construct($atts)
    {
        $this->atts = $atts['data'];
    }

    public function render()
    {
        // Init the styles array
        $styles = array(
            1 => '',
            2 => 'headline-style-1',
            3 => 'headline-style-2',
            4 => 'headline-style-3',
            5 => 'headline-style-4',
            6 => 'headline-style-5',
            7 => 'headline-style-6',
            8 => 'headline-style-7',
            9 => 'headline-style-8',
            10 => 'headline-style-9',
            11 => 'headline-style-10',
            12 => 'headline-style-11',
            13 => 'headline-style-12',
            14 => 'headline-style-13',
            15 => 'headline-style-14',
            16 => 'headline-style-15',
        );

        $effects = array(
            'type'          => array(
                'primary'   => ' letters type',
                'secondary' => ' waiting'
            ),
            'type_fast'          => array(
                'primary'   => ' letters type_fast',
                'secondary' => ' waiting_for_fast'
            ),
            'rotate-1'      => array(
                'primary'   => ' rotate-1',
                'secondary' => ''
            ),
            'rotate-2'      => array(
                'primary'   => ' letters rotate-2',
                'secondary' => ''
            ),
            'rotate-3'      => array(
                'primary'   => ' letters rotate-3',
                'secondary' => ''
            ),
            'loading-bar'   => array(
                'primary'   => ' loading-bar',
                'secondary' => ''
            ),
            'slide'         => array(
                'primary'   => ' slide',
                'secondary' => ''
            ),
            'clip'          => array(
                'primary'   => ' clip is-full-width',
                'secondary' => ''
            ),
            'zoom'          => array(
                'primary'   => ' zoom',
                'secondary' => ''
            ),
            'scale'         => array(
                'primary'   => ' letters scale',
                'secondary' => ''
            ),
            'push'          => array(
                'primary'   => ' push',
                'secondary' => ''
            )
        );

        if (isset($styles[$this->atts['style']])) {
            //Set the path for the images
            $path = $this->atts['image_path'];

            //Init flag for surrounding headline with span tag
            $span = false;

            //Init the before and after HTML variables
            $before = $after = '';

            //Set the fade images
            $fadeimgs = '<img src="' . $path . 'fade-left.png" alt="fade-left" width="120" height="10" class="fade-left" /><img src="' . $path . 'fade-right.png" alt="fade-right" width="120" height="10" class="fade-right" />';

            //Init the templates array that determines whether we use spans and before and after tags
            $tpls = array(
                8 => array('span' => true),
                10 => array('span' => true),
                14 => array('span' => true, 'before' => $fadeimgs),
                15 => array('span' => true, 'before' => $fadeimgs)
            );

            //If this style is set, extract it's variables
            if (isset($tpls[$this->atts['style']])) {
                extract($tpls[$this->atts['style']]);
            }

            //Surround headline with span if style has one and any before and after tags
            $str = $before . ($span ? '<span>' . $this->atts['content'] . '</span>' : $this->atts['content']) . $after;

            //Init the style string
            $style_str = ' class="op-headline' . $effects[$this->atts['effect']]['primary'] . ' ' . $styles[$this->atts['style']] . '"';

            //Init styling properties
            $chks = array(
                'align' => 'text-align',
                'line_height' => array('line-height','px'),
                'highlight' => 'background-color',
                'top_margin' => array('margin-top','px'),
                'bottom_margin' => array('margin-bottom','px')
            );

            //Loop through each property
            foreach ($chks as $var => $chk) {
                if (!empty($this->atts[$var])) $this->atts['font'] .= (is_array($chk) ? $chk[0] . ':' . $this->atts[$var] . $chk[1] . ';' : $chk . ':' . $this->atts[$var] . ';');
            }

            //Surroung styling with style HTML attribute, if styles exist
            $style_str .= (!empty($this->atts['font']) ? ' style=\'' . $this->atts['font'] . '\'' : '');

            $return_html = '';

            $return_html .= '
            <style type="text/css">
            #' . $this->atts['element_id'] . '.op-headline .op-words-wrapper * {color: ' . $this->atts['accent'] . '}
            #' . $this->atts['element_id'] . '.op-headline.type .op-words-wrapper::after {background-color: ' . $this->atts['accent'] . '}
            #' . $this->atts['element_id'] . '.op-headline.type_fast .op-words-wrapper::after {background-color: ' . $this->atts['accent'] . '}
            </style>';

            $return_html .= '
            <' . $this->atts['headline_tag'] . $style_str . ' id="' . $this->atts['element_id'] . '">
                <span>' . $str . '</span>';
            if (count($this->atts['parts']) > 0) {
                    $return_html .= '
                    <span class="op-words-wrapper' . $effects[$this->atts['effect']]['secondary'] . '">
                    ';
                $first = true;
                foreach ($this->atts['parts'] as $value) {
                    if ($first) {
                        $return_html .= '<b class="is-visible">' . $value . '</b>';
                    } else {
                        $return_html .= '<b>' . $value . '</b>';
                    }
                    $first = false;
                }
                $return_html .= '
                </span>';
            }
            $return_html .= '
            </' . $this->atts['headline_tag'] . '>';

            //Return the HTML
            return array('markup' => $return_html);
        }
    }
}
