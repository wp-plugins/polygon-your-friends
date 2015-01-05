<?php
/*
  Plugin Name: Polygon your friends information
  Plugin URI: http://CafeFreelancer.com
  Description: Your friends with avatar and information
  Version: 1.0
  Author: CafeFreelancer.com
  Author URI: http://CafeFreelancer.com
  License: GPLv2
 */

require_once(ABSPATH . WPINC . '/default-widgets.php');

function POLYGON_Your_Friends() {
    register_widget("POLYGON_Widget_Your_Friends");
}
add_action("widgets_init", "POLYGON_Your_Friends");

class POLYGON_Widget_Your_Friends extends WP_Widget {
    function __construct() {
        parent::__construct(
                'Polygon_widget_your_friend_info',
                __('Polygon your friends', 'POLYGON_Widget_Your_Friends'),
                array('description' => __('This widget support display user friends with information', 'POLYGON_Widget_Your_Friends'),)
        );
    }

    function widget($args, $instance) {
        $cache = wp_cache_get('polygon_widget_userfriend', 'widget');
		
		if (!is_array($cache))
            $cache = array();

        if (!isset($args['widget_id']))
            $args['widget_id'] = $this->id;

        if (isset($cache[$args['widget_id']])) {
            echo $cache[$args['widget_id']];
            return;
        }
        
                //Sort cus;
        if (!function_exists('UserCreateDateDESC')){
                function UserCreateDateDESC($a,$b)
                {
                if ($a->user_registered==$b->user_registered) return 0;
                return ($a->user_registered>$b->user_registered)?-1:1;
        }}
        if (!function_exists('UserCreateDateASC')){
                function UserCreateDateASC($a,$b)
                {
                if ($a->user_registered==$b->user_registered) return 0;
                return ($a->user_registered<$b->user_registered)?-1:1;
        }}if (!function_exists('UserNameDESC')){
                function UserNameDESC($a,$b)
                {
                if ($a->display_name==$b->display_name) return 0;
                return ($a->display_name>$b->display_name)?-1:1;
        }}if (!function_exists('UserNameASC')){
                function UserNameASC($a,$b)
                {
                if ($a->display_name==$b->display_name) return 0;
                return ($a->display_name<$b->display_name)?-1:1;
        }}
if (!function_exists('polygon_validate_gravatar')) {

            function polygon_validate_gravatar($email) {
                $hash = md5(strtolower(trim($email)));
                $uri = 'http://www.gravatar.com/avatar/' . $hash . '?d=404';
                $headers = @get_headers($uri);
                if (!preg_match("|200|", $headers[0])) {
                    $has_valid_avatar = FALSE;
                } else {
                    $has_valid_avatar = TRUE;
                }
                return $has_valid_avatar;
            }
        }
        //End sort cus;
        extract($args, EXTR_SKIP);
		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : '';
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
                $num_friend_display = (isset($instance['num_friend_display']) ) ? $instance['num_friend_display'] : 8;
                $avatar_size = (isset($instance['avatar_size']) ) ? $instance['avatar_size'] : 96;
                $height_show = (isset($instance['height_show']) ) ? $instance['height_show'] : 200;
		$rolelist = (isset($instance['rolelist']) ) ? $instance['rolelist'] : '';
                $avatar_layout = (isset($instance['avatar_layout']) ) ? $instance['avatar_layout'] : 'square';
                $sort_user = (isset($instance['sort_user']) ) ? $instance['sort_user'] : 'shuffle';
                $show_gravatar = (isset($instance['show_gravatar'])) ? $instance['show_gravatar'] : 'false';
    
		$output .=$before_widget;
		if ( $title ) $output .=$before_title . $title . $after_title;
		$matchSrc = "/src=[\"' ]?([^\"' >]+)[\"' ]?[^>]*>/i" ;
                $output.='<style>.circle{border-radius: 50%;}.square{border-radius: 0%;}.eclip1{border-top-left-radius: 50%; border-top-right-radius: 0%; border-bottom-right-radius: 50%; border-bottom-left-radius: 0%;}.eclip2{border-top-left-radius: 0%; border-top-right-radius: 50%; border-bottom-right-radius: 0%; border-bottom-left-radius: 50%;}.eclip3{border-radius: 20% 50%;}.eclip4{border-radius: 50% 20%;}</style>';
		$output .= '<div style="max-height:'.$height_show.'px;overflow-y:auto;overflow-x:hidden"><ul>';
                if($rolelist!=null){
                    $members =array();
                foreach ($rolelist as $itm) {
                    $currenmember=get_users('blog_id=1&orderby=user_registered&order=ASC&role='.$itm);
                    $members =array_merge($members,$currenmember);
                }
                switch($sort_user){
                    case 'usernamedesc': usort($members,  "UserNameDESC"); break;
                    case 'usernameasc': usort($members,  "UserNameASC"); break;
                    case 'createdatedesc': usort($members,  "UserCreateDateDESC"); break;
                    case 'createdateasc': usort($members,  "UserCreateDateASC"); break;
                    case 'shuffle':shuffle($members);break;
                }
				$ndisplay=0;
		foreach ($members as $user) {
		if($ndisplay==$num_friend_display){
			break;
		}
		$ndisplay++;
					$email=$user->user_email;
				//Display Gravatar;
				if ($show_gravatar == true && polygon_validate_gravatar($email)) {
                    $avatar = get_Gravatar_Author($email, $avatar_size);
                } else {
                    $avatar = get_avatar($email, $avatar_size);
                }

                preg_match($matchSrc, $avatar, $matches);
					$theImageUrl = $matches[1];
					$output .= '<li style="text-align:left;margin-bottom:10px">';
						
					$output .= '<a href="'.$user->user_url.'" rel="dofollow" target="_blank">
			<img class="'.$avatar_layout.'" src="'.$theImageUrl.'" width="'.$avatar_size.'" height="'.$avatar_size.'" style="float: left; margin-right: 10px;  background-color: rgb(255, 255, 255); padding: 3px; border: 1px solid rgb(214, 214, 214); width: '.$avatar_size.'px; height: '.$avatar_size.'px;"/>
			'.$user->display_name.'
			</a>
			<p>'.$user->description.'</p><div style="clear:both"></div>';
						
					$output .= '</li>';
				}
                }
				$output .= '</ul></div>';
			$output .= $after_widget;
echo $output;
				$cache[$args['widget_id']] = $output;
				wp_cache_set('polygon_widget_userfriend', $cache, 'widget');
                                
                                
                                
    }
    

    public function form($instance) {

       $title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : __('Polygon your friends', 'POLYGON_Widget_Your_Friends');
       $num_friend_display = (isset($instance['num_friend_display']))?((!empty($instance['num_friend_display']) ) ? $instance['num_friend_display'] : 8):8;
       $avatar_size = (isset($instance['avatar_size']))?((!empty($instance['avatar_size']) ) ? $instance['avatar_size'] : 96):96;
       $height_show = (isset($instance['height_show']))?((!empty($instance['height_show']) ) ? $instance['height_show'] : 200):200;
    $avatar_layout = (isset($instance['avatar_layout']))?((!empty($instance['avatar_layout']) ) ? $instance['avatar_layout'] : 'square'):'square';
    $sort_user = (isset($instance['sort_user']))?((!empty($instance['sort_user']) ) ? $instance['sort_user'] : 'shuffle'):'shuffle';
    $show_gravatar = (!empty($instance['show_gravatar']) ) ? $instance['show_gravatar'] : 'false';
       $rolelist = (isset($instance['rolelist']) ) ? $instance['rolelist'] : null;	
       ?>
			<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
                          
            <label for="<?php echo $this->get_field_id('num_friend_display'); ?>"><?php _e('Number friends display (<=50):'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('num_friend_display'); ?>" name="<?php echo $this->get_field_name('num_friend_display'); ?>" type="text" value="<?php echo esc_attr($num_friend_display); ?>" />
            <br/>
<input class="widefat" id="<?php echo $this->get_field_id('show_gravatar'); ?>" name="<?php echo $this->get_field_name('show_gravatar'); ?>" type="checkbox" <?php checked($instance['show_gravatar'], 'on'); ?> />
<label for="<?php echo $this->get_field_id('show_gravatar'); ?>"><?php _e('Show gravatar ?'); ?></label><br/>
            <label for="<?php echo $this->get_field_id('avatar_size'); ?>"><?php _e('Avatar size:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('avatar_size'); ?>" name="<?php echo $this->get_field_name('avatar_size'); ?>" type="text" value="<?php echo esc_attr($avatar_size); ?>" />
            
            <label for="<?php echo $this->get_field_id('avatar_layout'); ?>"><?php _e('Style:'); ?></label> 
            <select name="<?php echo $this->get_field_name('avatar_layout'); ?>" id="<?php echo $this->get_field_id('avatar_layout'); ?>">
                <option value='square' <?php echo ($avatar_layout == 'square') ? 'selected' : '' ?>>Square</option>
                <option value='circle'  <?php echo ($avatar_layout == 'circle') ? 'selected' : '' ?>>Circle</option>
                <option value='eclip1'  <?php echo ($avatar_layout == 'eclip1') ? 'selected' : '' ?>>Eclip 1</option>
                <option value='eclip2'  <?php echo ($avatar_layout == 'eclip2') ? 'selected' : '' ?>>Eclip 2</option>
                <option value='eclip3'  <?php echo ($avatar_layout == 'eclip3') ? 'selected' : '' ?>>Eclip 3</option>
                <option value='eclip4'  <?php echo ($avatar_layout == 'eclip4') ? 'selected' : '' ?>>Eclip 4</option>
            </select>
            <br/>
            <label for="<?php echo $this->get_field_id('sort_user'); ?>"><?php _e('Sort by:'); ?></label> 
            <select name="<?php echo $this->get_field_name('sort_user'); ?>" id="<?php echo $this->get_field_id('sort_user'); ?>">
                <option value='shuffle' <?php echo ($sort_user == 'shuffle') ? 'selected' : '' ?>>Shuffle</option>
                <option value='createdatedesc'  <?php echo ($sort_user == 'createdatedesc') ? 'selected' : '' ?>>Create date DESC</option>
                <option value='createdatedasc'  <?php echo ($sort_user == 'createdateasc') ? 'selected' : '' ?>>Create date ASC</option>
                <option value='usernamedesc'  <?php echo ($sort_user == 'usernamedesc') ? 'selected' : '' ?>>Name DESC</option>
                <option value='usernameasc'  <?php echo ($sort_user == 'usernameasc') ? 'selected' : '' ?>>Name ASC</option>
            </select><br/>
            <label for="<?php echo $this->get_field_id('rolelist'); ?>"><?php _e('Display by user roles:'); ?></label><br/>
<?php
	global $wp_roles;
	foreach ( $wp_roles->role_names as $role => $name ){
            ?>
            <input id="<?php echo $this->get_field_id('rolelist') . $role; ?>" name="<?php echo $this->get_field_name('rolelist'); ?>[]" type="checkbox" value="<?php echo $role; ?>" <?php echo (($rolelist!=null)? ((in_array($role,$rolelist))?'checked':''):''); ?>><?php echo $role;?></input><br/>
<?php
                
        }?>
            <br/>
            <label for="<?php echo $this->get_field_id('height_show'); ?>"><?php _e('Height show (for scroll):'); ?></label> 
                <input class="widefat" id="<?php echo $this->get_field_id('height_show'); ?>" name="<?php echo $this->get_field_name('height_show'); ?>" type="text" value="<?php echo esc_attr($height_show); ?>" />
	<?php
    }

    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
	$instance['title'] = strip_tags($new_instance['title']);
        $instance['num_friend_display'] = (!empty($new_instance['num_friend_display']) ) ? ($new_instance['num_friend_display'] <= 50) ? $new_instance['num_friend_display'] : 8 : 8;
        $instance['avatar_size'] = (!empty($new_instance['avatar_size']) ) ? ($new_instance['avatar_size'] <= 96) ? $new_instance['avatar_size'] : 96 : 96;
        $instance['avatar_layout'] =  $new_instance['avatar_layout'];
        $instance['sort_user'] =  $new_instance['sort_user'];
	$instance['rolelist']= $new_instance['rolelist'];
        $instance['show_gravatar'] = strip_tags($new_instance['show_gravatar']);
        $instance['height_show']= (!empty($new_instance['height_show']) ) ? ($new_instance['height_show'] <= 500) ? $new_instance['height_show'] : 200 : 200;
        return $instance;
    }
 
}
