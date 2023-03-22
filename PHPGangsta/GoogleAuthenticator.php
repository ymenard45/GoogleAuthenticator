<?php
/*
Plugin Name: Mon Google Authentificator
Plugin URI: https://sitewebprodesign.fr
Description: Un plugin d'authentification à deux facteurs pour WordPress en utilisant Google Authenticator
Version: 1.0
Author: Y.MENARD
Author URI: https://sitewebprodesign.fr
License: GPLv2 or later
Text Domain: mon-google-authentificator
*/

if (!defined('ABSPATH')) {
    exit; // Empêcher l'accès direct au fichier
}

function mon_google_authenticator_load_textdomain() {
    load_plugin_textdomain('mon-google-authenticator', false, basename(dirname(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'mon_google_authenticator_load_textdomain');

require_once plugin_dir_path(__FILE__) . 'lib/Google-Authentificator.php';

// Vos chaînes de texte originales
$menu_title = __('Mon Google Authenticator', 'mon-google-authenticator');
$section_title = __('Paramètres de Mon Google Authenticator', 'mon-google-authenticator');
$logo_url_label = __('URL du logo personnalisé', 'mon-google-authenticator');
$user_types_label = __('Forcer 2FA pour ces Types d\'utilisateurs', 'mon-google-authenticator');

// Anonymisation des messages d'erreur
Function my_google_authenticator_anonymize_error_messages($error_message, $locale = 'en') {
    switch ($locale) {
        case 'fr_FR':
            if (strpos($error_message, __('L\'identifiant', 'mon-google-authenticator')) !== false || strpos($error_message, __('adresse e-mail', 'mon-google-authenticator')) !== false || strpos($error_message, __('mot de passe', 'mon-google-authenticator')) !== false) {
                return __('Les informations d\'identification fournies sont incorrectes. Veuillez réessayer.', 'mon-google-authenticator');
            }
            break;
		case 'es_ES':
            if (strpos($error_message, __('Nombre de usuario', 'mon-google-authenticator')) !== false || strpos($error_message, __('correo electrónico', 'mon-google-authenticator')) !== false || strpos($error_message, __('contraseña', 'mon-google-authenticator')) !== false) {
                return __('Las credenciales proporcionadas son incorrectas. Por favor, inténtalo de nuevo.', 'mon-google-authenticator');
            }
            break;	
       case 'zh_CN':
            // Mandarin translation
                if (strpos($error_message, __('L\'identifiant', 'mon-google-authenticator')) !== false || strpos($error_message, __('adresse e-mail', 'mon-google-authenticator')) !== false || strpos($error_message, __('mot de passe', 'mon-google-authenticator')) !== false) {
                return __('提供的凭据信息错误。请重试。', 'mon-google-authenticator');
				}
            break;
        case 'ru_RU':
            // Russian translation
               if (strpos($error_message, __('L\'identifiant', 'mon-google-authenticator')) !== false || strpos($error_message, __('adresse e-mail', 'mon-google-authenticator')) !== false || strpos($error_message, __('mot de passe', 'mon-google-authenticator')) !== false) {
                return __(' Указанные учетные данные неверны. Пожалуйста, попробуйте еще раз.', 'mon-google-authenticator');
			   }
			break;
        case 'it_IT':
            // Italian translation
               if (strpos($error_message, __('L\'identifiant', 'mon-google-authenticator')) !== false || strpos($error_message, __('adresse e-mail', 'mon-google-authenticator')) !== false || strpos($error_message, __('mot de passe', 'mon-google-authenticator')) !== false) {
                return __(' Le credenziali fornite non sono corrette. Si prega di riprovare.', 'mon-google-authenticator');
			   }
			break;
        case 'de_DE':
            // German translation
               if (strpos($error_message, __('L\'identifiant', 'mon-google-authenticator')) !== false || strpos($error_message, __('adresse e-mail', 'mon-google-authenticator')) !== false || strpos($error_message, __('mot de passe', 'mon-google-authenticator')) !== false) {
                return __('Die angegebenen Anmeldeinformationen sind falsch. Bitte versuchen Sie es erneut.', 'mon-google-authenticator');
			   }
			break;
        case 'pt_PT':
            // Portuguese translation
               if (strpos($error_message, __('L\'identifiant', 'mon-google-authenticator')) !== false || strpos($error_message, __('adresse e-mail', 'mon-google-authenticator')) !== false || strpos($error_message, __('mot de passe', 'mon-google-authenticator')) !== false) {
                return __('As credenciais fornecidas estão incorretas. Por favor, tente novamente.', 'mon-google-authenticator');
			   }
			break;
        case 'hi_IN':
            // Hindi translation
               if (strpos($error_message, __('L\'identifiant', 'mon-google-authenticator')) !== false || strpos($error_message, __('adresse e-mail', 'mon-google-authenticator')) !== false || strpos($error_message, __('mot de passe', 'mon-google-authenticator')) !== false) {
                return __('प्रदान की गई प्रमाणीकरण जानकारी गलत है। कृपया पुनः प्रयास करें।', 'mon-google-authenticator');
			   }
			break;
        case 'bn_BD':
            // Bengali translation
               if (strpos($error_message, __('L\'identifiant', 'mon-google-authenticator')) !== false || strpos($error_message, __('adresse e-mail', 'mon-google-authenticator')) !== false || strpos($error_message, __('mot de passe', 'mon-google-authenticator')) !== false) {
                return __(' প্রদানকৃত প্রমাণদানের তথ্য ভুল। দয়া করে আবার চেষ্টা করুন।', 'mon-google-authenticator');
			   }
			break;
        case 'id_ID':
            // Indonesian translation
               if (strpos($error_message, __('L\'identifiant', 'mon-google-authenticator')) !== false || strpos($error_message, __('adresse e-mail', 'mon-google-authenticator')) !== false || strpos($error_message, __('mot de passe', 'mon-google-authenticator')) !== false) {
                return __('Informasi kredensial yang diberikan salah. Silakan coba lagi', 'mon-google-authenticator');
			   }
			break;
        
		default:
            // English translation
             if (strpos($error_message, __('Username', 'mon-google-authenticator')) !== false || strpos($error_message, __('email', 'mon-google-authenticator')) !== false || strpos($error_message, __('password', 'mon-google-authenticator')) !== false) {
                return __('The provided credentials are incorrect. Please try again.', 'mon-google-authenticator');
            }
            break;

			
			
			
    }
    return $error_message;
}

add_filter('login_errors', function($error_message) {
	
    // Get the current locale
    $locale = get_locale();

    // Call the function with the current locale
    return my_google_authenticator_anonymize_error_messages($error_message, $locale);
});
add_filter('login_errors', 'my_google_authenticator_anonymize_error_messages');



function my_google_authenticator_generate_secret() {
    $ga = new PHPGangsta_GoogleAuthenticator();
    return $ga->createSecret();
}

function my_google_authenticator_generate_qr_code_url($email, $secret) {
    $ga = new PHPGangsta_GoogleAuthenticator();
    return $ga->getQRCodeGoogleUrl($email, $secret);
}

function my_google_authenticator_profile_fields($user) {
    if (!current_user_can('edit_user', $user->ID)) {
        return;
    }

    $google_authenticator_enabled = get_user_meta($user->ID, 'google_authenticator_enabled', true);
    $google_authenticator_secret = get_user_meta($user->ID, 'google_authenticator_secret', true);

    // Générer un nouveau secret s'il n'existe pas encore
    if (empty($google_authenticator_secret)) {
        $google_authenticator_secret = my_google_authenticator_generate_secret();
        update_user_meta($user->ID, 'google_authenticator_secret', $google_authenticator_secret);
    }

    // Générer le QR code
    $qr_code_url = my_google_authenticator_generate_qr_code_url(get_bloginfo('name') . ':' . $user->user_login, $google_authenticator_secret);

    ?>
    <h3><?php _e('Authentification à deux facteurs (Google Authenticator)', 'mon-google-authenticator'); ?></h3>

    <table class="form-table">
        <tr>
            <th><label for="google_authenticator_enabled"><?php _e('Activer l\'authentification à deux facteurs', 'mon-google-authenticator'); ?></label></th>
            <td>
                <input type="checkbox" name="google_authenticator_enabled" id="google_authenticator_enabled" value="1" <?php checked($google_authenticator_enabled, 1); ?>>
            </td>
        </tr>
        <tr>
            <th><label for="google_authenticator_qr_code"><?php _e('QR code', 'mon-google-authenticator'); ?></label></th>
            <td>
                <img src="<?php echo esc_url($qr_code_url); ?>" alt="<?php _e('QR code pour Google Authenticator', 'mon-google-authenticator'); ?>">
                <p class="description"><?php _e('Scannez ce QR code avec l\'application Google Authenticator pour configurer l\'authentification à deux facteurs.', 'mon-google-authenticator'); ?></p>
            </td>
        </tr>
    </table>
    <?php
}

function my_google_authenticator_save_profile_fields($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    // Enregistrer l'état de l'authentification à deux facteurs
    if (isset($_POST['google_authenticator_enabled'])) {
        update_user_meta($user_id, 'google_authenticator_enabled', 1);

        // Récupérer l'email de l'utilisateur
        $user_email = get_userdata($user_id)->user_email;

        // Générer le nouveau QR code
        $google_authenticator_secret = get_user_meta($user_id, 'google_authenticator_secret', true);
        $qr_code_url = my_google_authenticator_generate_qr_code_url(get_bloginfo('name') . ':' . $user_login, $google_authenticator_secret);

        // Envoyer l'email
        $subject = 'Activation de l\'authentification à deux facteurs pour votre compte sur ' . get_bloginfo('name');
        $message = 'Bonjour,' . "\r\n\r\n" . 'L\'administrateur a activé l\'authentification à deux facteurs pour votre compte sur ' . get_bloginfo('name') . '.' . "\r\n\r\n" . 'Veuillez scanner le QR code ci-dessous dans l\'application Google Authenticator pour terminer la configuration.' . "\r\n\r\n" . $qr_code_url . "\r\n\r\n" . 'Cordialement, l\'équipe de ' . get_bloginfo('name');
        wp_mail($user_email, $subject, $message);
    } else {
        update_user_meta($user_id, 'google_authenticator_enabled', 0);
    }
}

add_action('show_user_profile', 'my_google_authenticator_profile_fields');
add_action('edit_user_profile', 'my_google_authenticator_profile_fields');
add_action('personal_options_update', 'my_google_authenticator_save_profile_fields');
add_action('edit_user_profile_update', 'my_google_authenticator_save_profile_fields');

function show_google_authenticator_fields() {
    echo '<style>tr.google-authenticator-fields {display:table-row !important;}</style>';
}
add_action('admin_head', 'show_google_authenticator_fields');



function my_google_authenticator_authenticate($user, $username, $password) {
    if (is_wp_error($user)) {
        return $user;
    }

    $google_authenticator_enabled = get_user_meta($user->ID, 'google_authenticator_enabled', true);

    // Vérifier si l'authentification à deux facteurs est activée pour cet utilisateur
    if ($google_authenticator_enabled) {
        if (!isset($_POST['google_authenticator_code'])) {
            return new WP_Error('authentication_failed', __('L\'authentification à deux facteurs est requise.'));
        }

        $ga = new PHPGangsta_GoogleAuthenticator();
        $secret = get_user_meta($user->ID, 'google_authenticator_secret', true);
        $code = $_POST['google_authenticator_code'];

        if (!$ga->verifyCode($secret, $code)) {
            return new WP_Error('authentication_failed', __('Le code Google Authenticator entré est incorrect.'));
        }
    }

    return $user;
}

add_filter('authenticate', 'my_google_authenticator_authenticate', 40, 3);

function my_google_authenticator_login_form() {
    ?>
    <p>
        <label for="google_authenticator_code"><?php _e('Code Google Authenticator') ?><br />
        <input type="text" name="google_authenticator_code" id="google_authenticator_code" class="input" size="20" autocomplete="off" />
    </p>
    <?php
}

add_action('login_form', 'my_google_authenticator_login_form');


function my_google_authenticator_section_callback() {
    echo __('Personnalisez les paramètres de votre plugin Google Authenticator.', 'my-google-authenticator');
}

function my_google_authenticator_options_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('my_google_authenticator');
            do_settings_sections('my_google_authenticator');
            submit_button(__('Enregistrer les modifications', 'my-google-authenticator'));
            ?>
        </form>
    </div>
    <?php
}
function my_google_authenticator_settings_init() {
    register_setting('my_google_authenticator', 'my_google_authenticator_settings');

    add_settings_section(
        'my_google_authenticator_section',
        __('Paramètres de My Google Authenticator', 'my-google-authenticator'),
        'my_google_authenticator_section_callback',
        'my_google_authenticator'
    );

    add_settings_field(
        'my_google_authenticator_logo_url',
        __('URL du logo personnalisé', 'my-google-authenticator'),
        'my_google_authenticator_logo_url_callback',
        'my_google_authenticator',
        'my_google_authenticator_section'
    );

    add_settings_field(
        'my_google_authenticator_user_types',
        __('Types d\'utilisateurs autorisés', 'my-google-authenticator'),
        'my_google_authenticator_user_types_callback',
        'my_google_authenticator',
        'my_google_authenticator_section'
    );
	
				// Ajout des champs pour les URL des fonds d'écran
			add_settings_field(
				'desktop_wallpaper_url', // ID du champ
				__('Desktop wallpaper URL', 'my-google-authenticator'), // Titre du champ
				'my_google_authenticator_desktop_wallpaper_callback', // Fonction de rappel pour afficher le champ
				'my_google_authenticator', // Page de réglage où afficher le champ
				'my_google_authenticator_section' // Section de réglage où afficher le champ
			);

			add_settings_field(
				'mobile_wallpaper_url',
				__('Mobile wallpaper URL', 'my-google-authenticator'),
				'my_google_authenticator_mobile_wallpaper_callback',
				'my_google_authenticator',
				'my_google_authenticator_section'
			);

			// Ajout du champ pour le niveau de transparence
			add_settings_field(
				'transparency_level',
				__('Transparency level', 'my-google-authenticator'),
				'my_google_authenticator_transparency_callback',
				'my_google_authenticator',
				'my_google_authenticator_section'
			);

	
	
}

add_action('admin_init', 'my_google_authenticator_settings_init');

function my_google_authenticator_desktop_wallpaper_callback() {
    $options = get_option('my_google_authenticator_options');
    echo '<input type="text" id="desktop_wallpaper_url" name="my_google_authenticator_options[desktop_wallpaper_url]" value="' . esc_attr($options['desktop_wallpaper_url']) . '">';
}

function my_google_authenticator_mobile_wallpaper_callback() {
    $options = get_option('my_google_authenticator_options');
    echo '<input type="text" id="mobile_wallpaper_url" name="my_google_authenticator_options[mobile_wallpaper_url]" value="' . esc_attr($options['mobile_wallpaper_url']) . '">';
}

function my_google_authenticator_transparency_callback() {
    $options = get_option('my_google_authenticator_options');
    echo '<input type="number" id="transparency_level" name="my_google_authenticator_options[transparency_level]" value="' . esc_attr($options['transparency_level']) . '" min="10" max="50">';
}




// ajout logo
// ajout logo
function my_google_authenticator_replace_wp_logo() {
    $settings = get_option('my_google_authenticator_settings');
    $logo_url = isset($settings['logo_url']) ? esc_attr($settings['logo_url']) : plugin_dir_url(__FILE__) . 'images/logo.png';
    ?>
    <style type="text/css">
        #login h1 a {
            background-image: url("<?php echo $logo_url; ?>");
            background-size: contain;
            max-width: 400px;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            pointer-events: none;
        }

        #login h1 a[href="/"]{
            display:none;	
            pointer-events: none !important;
        }

        #login h1 a:hover {
            display: block;
            pointer-events: none !important;
        }
    </style>
    <?php
}
add_action('login_enqueue_scripts', 'my_google_authenticator_replace_wp_logo');


// 
function my_google_authenticator_logo_url_callback() {
    $settings = get_option('my_google_authenticator_settings');
    $value = isset($settings['logo_url']) ? esc_attr($settings['logo_url']) : '';
    echo '<input type="url" size="75" name="my_google_authenticator_settings[logo_url]" value="' . $value . '" />';
}

function my_google_authenticator_user_types_callback() {
    $settings = get_option('my_google_authenticator_settings');
    $types = array('administrator', 'editor', 'author', 'contributor', 'subscriber');
    foreach ($types as $type) {
        $checked = isset($settings['user_types'][$type]) && $settings['user_types'][$type] == 'on' ? 'checked' : '';
        echo '<label><input type="checkbox" name="my_google_authenticator_settings[user_types][' . $type . ']" ' . $checked . ' /> ' . ucfirst($type) . '</label><br />';
    }
}

// ajout menu
function my_google_authenticator_add_settings_link() {
    add_options_page(
        __('My Google Authenticator', 'my-google-authenticator'),
        __('My Google Authenticator', 'my-google-authenticator'),
        'manage_options',
        'my_google_authenticator_settings',
        'my_google_authenticator_options_page',
		'my_google_authenticator_desktop_wallpaper_callback',
		'my_google_authenticator_mobile_wallpaper_callback',
        'my_google_authenticator_transparency_callback'
    );
}

add_action('admin_menu', 'my_google_authenticator_add_settings_link');

// ajout lien settings
function my_google_authenticator_plugin_action_links($links) {
    $settings_link = '<a href="' . esc_url(admin_url('options-general.php?page=my_google_authenticator_settings')) . '">' . __('Réglages', 'mon-google-authenticator') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'my_google_authenticator_plugin_action_links');


// pro

function my_google_authenticator_custom_login_style() {
    // Récupérez les options
    $options = get_option('my_google_authenticator_options');
    $desktop_wallpaper_url = esc_url($options['desktop_wallpaper_url']);
    $mobile_wallpaper_url = esc_url($options['mobile_wallpaper_url']);
    $transparency_level = floatval($options['transparency_level']) / 100;
    $text_color = esc_attr($options['text_color']);

    // Générez le style personnalisé
    ?>
    <style type="text/css">
        body {
            background-size: cover;
        }

        @media (min-width: 768px) {
            body {
                background-image: url("<?php echo $desktop_wallpaper_url; ?>");
            }
        }

        @media (max-width: 767px) {
            body {
                background-image: url("<?php echo $mobile_wallpaper_url; ?>");
            }
        }

        .my-google-authenticator-login-form-wrapper {
            background-color: rgba(255, 255, 255, <?php echo $transparency_level; ?>);
            padding: 15px;
            border-radius: 4px;
        }

        body.login a,
        body.login #nav a,
        body.login #backtoblog a {
            color: <?php echo $text_color; ?> !important;
        }
    </style>
    <?php
}

add_action('login_enqueue_scripts', 'my_google_authenticator_custom_login_style');

function my_google_authenticator_login_form_wrapper() {
    echo '<div class="my-google-authenticator-login-form-wrapper"></div>';
}
add_action('login_form', 'my_google_authenticator_login_form_wrapper');
