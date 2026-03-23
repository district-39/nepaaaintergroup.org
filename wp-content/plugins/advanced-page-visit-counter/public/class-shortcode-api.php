<?php

class Shortcode_API
{
    public function __construct()
    {
        add_shortcode( 'apvc_widget', array( $this, 'render_shortcode' ) );
        add_shortcode( 'apvc_widget_embed', array( $this, 'render_shortcode' ) );
        add_filter( 'the_content', array( $this, 'render_shortcode_on_content' ) );
        add_shortcode( 'avc_visit_counter', array( $this, 'public_avc_visit_counter' ) );
        add_shortcode( 'apvc_embed', array( $this, 'public_avc_visit_counter' ) );
        //      add_shortcode( 'avc_visit_counter', array( $this, 'remove_old_shortcode' ) );
        //      add_shortcode( 'apvc_embed', array( $this, 'remove_old_shortcode' ) );
    }
    
    public function remove_old_shortcode( $atts = array(), $content = null, $tag = '' )
    {
        return '';
    }
    
    public function render_shortcode( $atts = array(), $content = null, $tag = '' )
    {
        $shortcodes = APVC_Shortcode_Templates::apvc_get_shortcode_templates();
        
        if ( isset( $atts['for_specific_post'] ) && 0 !== $atts['for_specific_post'] ) {
            $post_id = absint( $atts['for_specific_post'] );
        } else {
            $post_id = get_the_ID();
        }
        
        if ( 0 === absint( $post_id ) ) {
            $post_id = get_the_ID();
        }
        $atts['sec_key'] = rand( 0, 9999 );
        $start_el = '';
        $start_con = '';
        $start_end = '</div>';
        $div_class = '';
        $css_el_start = '<style>.apvc_shortcode_ren_' . $atts['sec_key'] . ' { text-align:center;';
        $css_el_end = '} </style>';
        $icon_html = '';
        if ( isset( $atts['show_icon'] ) && 1 == $atts['show_icon'] ) {
            $icon_html = '<img src="' . APVC_VIEW_ICON . '" class="apvc-view-icon" alt="apvc-icon">';
        }
        
        if ( !empty($atts['template']) ) {
            $div_class = $shortcodes[$atts['template']]['key'];
            $start_el .= '<style>' . $shortcodes[$atts['template']]['css'] . '</style>';
            if ( isset( $atts['show_current_page_total_visits'] ) ) {
                $start_con .= '<div>' . $icon_html . $atts['counter_label'] . ' ' . APVC_Query::get_current_page_counts( $post_id ) . '</div>';
            }
            if ( isset( $atts['show_today_visits'] ) ) {
                $start_con .= '<div>' . $icon_html . $atts['today_counter_label'] . ' ' . APVC_Query::get_current_page_today_counts( $post_id ) . '</div>';
            }
            if ( isset( $atts['show_global_visits'] ) ) {
                $start_con .= '<div>' . $icon_html . $atts['global_counter_label'] . ' ' . APVC_Query::get_total_counts() . '</div>';
            }
        } else {
            if ( !empty($atts['border_size']) ) {
                $css_el_start .= 'border: ' . $atts['border_size'] . 'px;';
            }
            if ( !empty($atts['border_style']) ) {
                $css_el_start .= 'border-style: ' . $atts['border_style'] . ';';
            }
            if ( !empty($atts['border_color']) ) {
                $css_el_start .= 'border-color: ' . $atts['border_color'] . ';';
            }
            if ( !empty($atts['border_radius']) ) {
                $css_el_start .= 'border-radius: ' . $atts['border_radius'] . 'px;';
            }
            if ( !empty($atts['font_color']) ) {
                $css_el_start .= 'color: ' . $atts['font_color'] . ';';
            }
            if ( !empty($atts['background_color']) ) {
                $css_el_start .= 'background-color: ' . $atts['background_color'] . ';';
            }
            if ( !empty($atts['font_style']) ) {
                $css_el_start .= 'font-style: ' . $atts['font_style'] . ';';
            }
            if ( !empty($atts['padding']) ) {
                $css_el_start .= 'padding: ' . $atts['padding'] . 'px;';
            }
            if ( !empty($atts['width']) ) {
                
                if ( is_admin() ) {
                    $css_el_start .= 'width: ' . $atts['width'] . 'px; max-width: 100%;';
                } else {
                    $css_el_start .= 'width: ' . $atts['width'] . 'px; max-width: ' . $atts['width'] . 'px;';
                }
            
            }
            if ( isset( $atts['show_current_page_total_visits'] ) ) {
                $start_con .= '<div>' . $icon_html . $atts['counter_label'] . ' ' . APVC_Query::get_current_page_counts( $post_id ) . '</div>';
            }
            if ( isset( $atts['show_today_visits'] ) ) {
                $start_con .= '<div>' . $icon_html . $atts['today_counter_label'] . ' ' . APVC_Query::get_current_page_today_counts( $post_id ) . '</div>';
            }
            if ( isset( $atts['show_global_visits'] ) ) {
                $start_con .= '<div>' . $icon_html . $atts['global_counter_label'] . ' ' . APVC_Query::get_total_counts() . '</div>';
            }
        }
        
        $start_el .= $css_el_start . $css_el_end;
        $start_el .= '<div class="apvc_shortcode_ren_' . $atts['sec_key'] . ' ' . $div_class . '">';
        return $start_el . $start_con . $start_end;
    }
    
    public function render_shortcode_on_content( $content )
    {
        if ( is_archive() ) {
            return $content;
        }
        $atts = get_option( 'apvc_widget_settings', array() );
        $widget_tmp_settings = get_option( 'apvc_widget_template_settings', array() );
        $widget_vis_settings = get_option( 'apvc_widget_visibility_settings', array() );
        $display_option = $atts['apvc_widget_display'];
        if ( !empty($display_option) && 'disable' === $display_option ) {
            return $content;
        }
        $atts['sec_key'] = rand( 0, 9999 );
        $post_id = get_the_ID();
        $start_el = '';
        $start_con = '';
        $div_class = '';
        $start_end = '</div>';
        $css_el_start = '<style>.apvc_shortcode_ren_' . $atts['sec_key'] . ' { text-align:center;';
        $css_el_end = '} </style>';
        $icon_html = '';
        if ( isset( $atts['apvc_show_icon'] ) && 'on' === $atts['apvc_show_icon'] ) {
            $icon_html = '<img src="' . APVC_VIEW_ICON . '" class="apvc-view-icon" alt="apvc-icon">';
        }
        
        if ( isset( $widget_tmp_settings['apvc_widget_templates'] ) && '-' !== $widget_tmp_settings['apvc_widget_templates'] ) {
            $shortcodes = APVC_Shortcode_Templates::apvc_get_shortcode_templates();
            $shortcode = $shortcodes[$widget_tmp_settings['apvc_widget_templates']] ?? '';
            $div_class = $shortcode['key'];
            $start_el .= '<style>' . $shortcode['css'] . '</style>';
            if ( isset( $widget_vis_settings['apvc_total_visits_cr_page'] ) && 'on' === $widget_vis_settings['apvc_total_visits_cr_page'] ) {
                $start_con .= '<div>' . $icon_html . $atts['apvc_default_label'] . ' ' . APVC_Query::get_current_page_counts( absint( $post_id ) ) . '</div>';
            }
            if ( isset( $widget_vis_settings['apvc_show_today_counts'] ) && 'on' === $widget_vis_settings['apvc_show_today_counts'] ) {
                $start_con .= '<div>' . $icon_html . $atts['apvc_today_label'] . ' ' . APVC_Query::get_current_page_today_counts( $post_id ) . '</div>';
            }
            if ( isset( $widget_vis_settings['apvc_show_total_counts'] ) && 'on' === $widget_vis_settings['apvc_show_total_counts'] ) {
                $start_con .= '<div>' . $icon_html . $atts['apvc_total_counts_label'] . ' ' . APVC_Query::get_total_counts() . '</div>';
            }
        } else {
            if ( isset( $atts['apvc_default_border_width'] ) && !empty($atts['apvc_default_border_width']) ) {
                $css_el_start .= 'border: ' . $atts['apvc_default_border_width'] . 'px;';
            }
            if ( isset( $atts['apvc_default_border_style'] ) && !empty($atts['apvc_default_border_style']) ) {
                $css_el_start .= 'border-style: ' . $atts['apvc_default_border_style'] . ';';
            }
            if ( isset( $atts['apvc_default_counter_border_color'] ) && !empty($atts['apvc_default_counter_border_color']) ) {
                $css_el_start .= 'border-color: ' . $atts['apvc_default_counter_border_color'] . ';';
            }
            if ( isset( $atts['apvc_default_border_radius'] ) && !empty($atts['apvc_default_border_radius']) ) {
                $css_el_start .= 'border-radius: ' . $atts['apvc_default_border_radius'] . 'px;';
            }
            if ( isset( $atts['apvc_default_counter_text_color'] ) && !empty($atts['apvc_default_counter_text_color']) ) {
                $css_el_start .= 'color: ' . $atts['apvc_default_counter_text_color'] . ';';
            }
            if ( isset( $atts['apvc_default_background_color'] ) && !empty($atts['apvc_default_background_color']) ) {
                $css_el_start .= 'background-color: ' . $atts['apvc_default_background_color'] . ';';
            }
            if ( isset( $atts['apvc_default_font_style'] ) && !empty($atts['apvc_default_font_style']) ) {
                $css_el_start .= 'font-style: ' . $atts['apvc_default_font_style'] . ';';
            }
            if ( isset( $atts['apvc_widget_padding'] ) && !empty($atts['apvc_widget_padding']) ) {
                $css_el_start .= 'padding: ' . $atts['apvc_widget_padding'] . 'px;';
            }
            if ( isset( $atts['apvc_widget_width'] ) && !empty($atts['apvc_widget_width']) ) {
                
                if ( is_admin() ) {
                    $css_el_start .= 'width: ' . $atts['apvc_widget_width'] . 'px; max-width: 100%;';
                } else {
                    $css_el_start .= 'width: ' . $atts['apvc_widget_width'] . 'px; max-width: ' . $atts['apvc_widget_width'] . 'px;';
                }
            
            }
            if ( isset( $widget_vis_settings['apvc_total_visits_cr_page'] ) && !empty($widget_vis_settings['apvc_total_visits_cr_page']) ) {
                $start_con .= '<div>' . $icon_html . $atts['apvc_default_label'] . ' ' . APVC_Query::get_current_page_counts( $post_id ) . '</div>';
            }
            if ( isset( $widget_vis_settings['apvc_show_today_counts'] ) && !empty($widget_vis_settings['apvc_show_today_counts']) ) {
                $start_con .= '<div>' . $icon_html . $atts['apvc_today_label'] . ' ' . APVC_Query::get_current_page_today_counts( $post_id ) . '</div>';
            }
            if ( isset( $widget_vis_settings['apvc_show_total_counts'] ) && !empty($widget_vis_settings['apvc_show_total_counts']) ) {
                $start_con .= '<div>' . $icon_html . $atts['apvc_total_counts_label'] . ' ' . APVC_Query::get_total_counts() . '</div>';
            }
        }
        
        $start_el .= $css_el_start . $css_el_end;
        $start_el .= '<div class="apvc_shortcode_ren_' . $atts['sec_key'] . ' ' . $div_class . '">';
        $shortcode_content = $start_el . $start_con . $start_end;
        if ( !empty($display_option) && 'above_the_content' === $display_option ) {
            return $shortcode_content . $content;
        }
        if ( !empty($display_option) && 'below_the_content' === $display_option ) {
            return $content . $shortcode_content;
        }
    }
    
    public function public_avc_visit_counter( $atts = array(), $content = null, $tag = '' )
    {
        global  $wpdb ;
        $atts = array_change_key_case( (array) $atts, CASE_LOWER );
        $s_html = '';
        $html = '';
        if ( $atts['current'] == 'false' && $atts['today'] == 'false' && $atts['global'] == 'false' ) {
            return false;
        }
        $type = $atts['type'];
        if ( isset( $atts['article_id'] ) ) {
            $article_id = $atts['article_id'];
        }
        if ( $atts['type'] !== 'individual' ) {
            if ( is_admin() ) {
                $article_id = 1;
            }
        }
        
        if ( $atts['type'] == 'individual' ) {
            $article_id = $atts['article_id'];
        } else {
            $article_id = 1;
        }
        
        
        if ( $type == 'individual' && !empty($article_id) ) {
            $pageCnt = APVC_Query::get_current_page_counts( $article_id );
        } elseif ( $type == 'global' ) {
            $pageCnt = APVC_Query::get_total_counts();
        } else {
            
            if ( is_admin() ) {
                $article_id = 1;
            } else {
                $article_id = get_the_ID();
            }
            
            $pageCnt = APVC_Query::get_current_page_today_counts( $article_id );
        }
        
        $active = get_post_meta( $article_id, 'apvc_active_counter', true );
        if ( $active == 'No' ) {
            return false;
        }
        $borderSize = ( !empty($atts['border_size']) ? $atts['border_size'] : 0 );
        $borderRadius = ( !empty($atts['border_radius']) ? 'border-radius:' . $atts['border_radius'] . 'px;' : '' );
        $borderStyle = ( !empty($atts['border_style']) ? $atts['border_style'] : 'solid' );
        $borderColor = ( !empty($atts['border_color']) ? '#' . $atts['border_color'] : '#000000' );
        
        if ( $borderSize != 0 ) {
            $borderCSS = 'border: ' . $borderSize . 'px ' . $borderStyle . ' ' . $borderColor . ';';
        } else {
            $borderCSS = '';
        }
        
        $bgColor = ( !empty($atts['background_color']) ? 'background-color: ' . '#' . $atts['background_color'] . ';' : '' );
        $font_size = ( !empty($atts['font_size']) ? 'font-size: ' . $atts['font_size'] . 'px;' : '' );
        
        if ( $atts['font_style'] == 'italic' ) {
            $font_style = ( !empty($atts['font_style']) ? 'font-style: ' . $atts['font_style'] . ';' : '' );
        } else {
            $font_style = ( !empty($atts['font_style']) ? 'font-weight: ' . $atts['font_style'] . ';' : '' );
        }
        
        $font_color = ( !empty($atts['font_color']) ? 'color:' . '#' . $atts['font_color'] . ';' : '' );
        $padding = ( !empty($atts['padding']) ? 'padding:' . $atts['padding'] . 'px;' : '' );
        $counter_label = ( empty($atts['counter_label']) ? ' Visits: ' : $atts['counter_label'] );
        $today_cnt_label = ( empty($atts['today_cnt_label']) ? $counter_label : $atts['today_cnt_label'] );
        $global_cnt_label = ( empty($atts['global_cnt_label']) ? $counter_label : $atts['global_cnt_label'] );
        $widget_label = get_post_meta( get_the_ID(), 'widget_label', true );
        if ( empty($widget_label) ) {
            $widget_label = $counter_label;
        }
        //      $base_count = get_post_meta( $article_id, 'count_start_from', true );
        //      if ( ! empty( $base_count ) && $base_count > 0 ) {
        //          $pageCnt = $pageCnt + $base_count;
        //      }
        
        if ( !isset( $atts['current'] ) ) {
            $pageCnt = $widget_label . ' ' . $pageCnt;
        } elseif ( $atts['current'] == 'true' ) {
            $pageCnt = $widget_label . ' ' . $pageCnt;
        } else {
            $pageCnt = '';
        }
        
        
        if ( $atts['today'] == 'true' ) {
            $TodaypageCnt = APVC_Query::get_current_page_today_counts( $article_id );
            $todaysCount = $today_cnt_label . ' ' . $TodaypageCnt;
        } else {
            $todaysCount = '';
        }
        
        
        if ( $atts['global'] == 'true' ) {
            $allTime = APVC_Query::get_total_counts();
            $allTimeCount = $global_cnt_label . ' ' . $allTime;
        } else {
            $allTimeCount = '';
        }
        
        
        if ( !empty($atts['width']) ) {
            $shWidth = ' width: 100%; max-width:' . $atts['width'] . 'px; margin: 0 auto;';
        } else {
            $shWidth = '';
        }
        
        $wid_templated = $atts['widget_template'];
        
        if ( $wid_templated != 'None' && !empty($wid_templated) ) {
            $shortcodeData = APVC_Shortcode_Templates::apvc_get_shortcode_templates( $wid_templated );
            
            if ( $shortcodeData['icon'] !== 'yes' ) {
                $s_html = APVC_Shortcodes::apvc_get_html_with_icon( $wid_templated, 'yes' );
            } else {
                $s_html = APVC_Shortcodes::apvc_get_html_without_icon( $wid_templated );
            }
            
            $crReplace = '<div>{current_visits_label}{current_visits_counts}</div>';
            $tdReplace = '<div>{today_visits_label}{today_visits_counts}</div>';
            $glReplace = '<div>{total_visits_label}{total_visits_counts}</div>';
            $iconCR = $iconGL = $iconTD = '';
            
            if ( !empty($pageCnt) ) {
                $s_html = str_replace( $crReplace, '<div>' . $pageCnt . '</div>', $s_html );
            } else {
                $s_html = str_replace( $crReplace, '', $s_html );
            }
            
            
            if ( !empty($todaysCount) ) {
                $s_html = str_replace( $tdReplace, '<div>' . $todaysCount . '</div>', $s_html );
            } else {
                $s_html = str_replace( $tdReplace, '', $s_html );
            }
            
            
            if ( !empty($allTimeCount) ) {
                $s_html = str_replace( $glReplace, '<div>' . $allTimeCount . '</div>', $s_html );
            } else {
                $s_html = str_replace( $glReplace, '', $s_html );
            }
            
            $s_html = str_replace( '{inline_style}', 'style="' . $shWidth . $padding . '; margin-bottom :15px;"', $s_html );
            $s_html = '<style type="text/css">' . $shortcodeData['css'] . '</style>' . $s_html;
        } else {
            $html = "<div class='avc_visit_counter_front' style='" . $borderCSS . $bgColor . $borderRadius . $font_size . $font_style . $font_color . '' . $shWidth . '' . $padding . "'>" . $pageCnt . ' ' . $todaysCount . ' ' . $allTimeCount . '</div>';
        }
        
        return $s_html . $html;
    }

}