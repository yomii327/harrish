<?php
//Database Login Details
error_reporting(0);
define('DEF_HOST', 'localhost');
define('DEF_USER', 'root');
define('DEF_PASSWORD', '');
define('DEF_DBNAME', 'dev_defectid_harrishmc');//defectid_harrishmcdev

// DB tables name
define('COMPANIES', 'pms_companies');
define('BUILDERS', 'user');
define('BUILDER', 'pms_builders');
define('DEFECTS', 'pms_defects');
define('OWNERS', 'pms_owners');
define('PROJECTS', 'user_projects');
define('RESPONSIBLES', 'pms_responsibles');
define('ASSIGN', 'pms_assign');
define('SUBBUILDERS', 'pms_builder_to_subbuilders');
define('DEFECTSLIST', 'pms_defects_list');
define('PROJECTDEFECTS', 'pms_pro_defects');
define('PROJECTLOCATION', 'project_locations');
define('PROJECT_MONITORING_LOCATION', 'project_monitoring_locations');
define('PROJECTINSPECTION', 'inspection_inspected_by');
define('PROJECTISSUETO', 'issued_to_for_inspections');

define('SMTPUSERNAME', "WiseworkingSystems@wiseworking.com.au"); // SMTP account username wiseworkingsales@gmail.com
define('SMTPPASSWORD', "Hotu1672"); // SMTP account password Wiseworking123

// Mailgun settings
define("smtpUsername", "postmaster@defectid.com");//postmaster@constructionid.com
define("smtpPassword","578d86cd0d7a4aba05069618d432cee3");//4354114c48ec5c61a9ffad7a04056f32
define('smtpHost', "smtp.mailgun.org"); // smtp.gmail.com
define('smtpPort', ""); // 465

// Website name
define('SITE_NAME', 'wiseworker | DefectID');

// Application name
define('APPS_NAME', 'DEFECTID');

// Localhost settings
#define('SITE_PATH', 'fxdev');
define('SITE_PATH', '');

// System admin email addsess
define('EMAIL', 'info@wiseworker.net');

// Redirect URL
define('DOMAIN', $_SERVER['SERVER_NAME']."/".SITE_PATH);

// Image path for pdf
define('IMG_SRC', 'http://'.DOMAIN);

// Access denied varaibles
define('ACCESS_DENIED_SCREEN', "http://".DOMAIN."pms.php?sect=access_denied");

// Company variables
define('COMPANY_ANALYSIS', "http://".DOMAIN."pms.php?sect=c_full_analysis");
define('COMPANY_DASHBOARD', "http://".DOMAIN."pms.php?sect=c_dashboard");

// Builder variables
define('REQ_SUCC', "Location: http://".DOMAIN."pms.php?sect=log&type=apply_now");
define('BUILDER_ANALYSIS', "http://".DOMAIN."pms.php?sect=b_full_analysis");
define('BUILDER_DASHBOARD', "http://".DOMAIN."pms.php?sect=b_full_analysis");
define('HOME_SCREEN', "http://".DOMAIN);
define('SHOW_PROJECTS', "http://".DOMAIN."pms.php?sect=show_project");

// Owner variables
define('OWNER_DASHBOARD', "http://".DOMAIN."pms.php?sect=o_dashboard");

// Account Activate location
define('ACTIVATE', "http://".DOMAIN."activate/");

// Responsible variables
define('RESPONSIBLE_DASHBOARD', "http://".DOMAIN."pms.php?sect=r_dashboard");
define('SHOW_ASSIGN_TO', "http://".DOMAIN."pms.php?sect=assign_to");

//Email summary url for redirect
define('EMAILURL', "http://".DOMAIN."pms.php?sect=builder&rd=aV9kZWZlY3QmYms9WQ==");

//Report Logo Path for iPad
define('REPORTLOGO', "http://".DOMAIN."/company_logo");

require_once ("permission.php");
#print_r(get_defined_constants(true));
?>
