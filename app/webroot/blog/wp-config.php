<?php
/**
 * In dieser Datei werden die Grundeinstellungen für WordPress vorgenommen.
 *
 * Zu diesen Einstellungen gehören: MySQL-Zugangsdaten, Tabellenpräfix,
 * Secret-Keys, Sprache und ABSPATH. Mehr Informationen zur wp-config.php gibt es auf der {@link http://codex.wordpress.org/Editing_wp-config.php
 * wp-config.php editieren} Seite im Codex. Die Informationen für die MySQL-Datenbank bekommst du von deinem Webhoster.
 *
 * Diese Datei wird von der wp-config.php-Erzeugungsroutine verwendet. Sie wird ausgeführt, wenn noch keine wp-config.php (aber eine wp-config-sample.php) vorhanden ist,
 * und die Installationsroutine (/wp-admin/install.php) aufgerufen wird.
 * Man kann aber auch direkt in dieser Datei alle Eingaben vornehmen und sie von wp-config-sample.php in wp-config.php umbenennen und die Installation starten.
 *
 * @package WordPress
 */

/**  MySQL Einstellungen - diese Angaben bekommst du von deinem Webhoster. */
/**  Ersetze database_name_here mit dem Namen der Datenbank, die du verwenden möchtest. */
define('DB_NAME', 'laudatio');

/** Ersetze username_here mit deinem MySQL-Datenbank-Benutzernamen */
define('DB_USER', 'laudatio');

/** Ersetze password_here mit deinem MySQL-Passwort */
define('DB_PASSWORD', 'Bagsajdesec9');

/** Ersetze localhost mit der MySQL-Serveradresse */
define('DB_HOST', 'mydb.cms.hu-berlin.de:3308');

/** Der Datenbankzeichensatz der beim Erstellen der Datenbanktabellen verwendet werden soll */
define('DB_CHARSET', 'utf8');

/** Der collate type sollte nicht geändert werden */
define('DB_COLLATE', '');

/**#@+
 * Sicherheitsschlüssel
 *
 * Ändere jeden KEY in eine beliebige, möglichst einzigartige Phrase. 
 * Auf der Seite {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service} kannst du dir alle KEYS generieren lassen.
 * Bitte trage für jeden KEY eine eigene Phrase ein. Du kannst die Schlüssel jederzeit wieder ändern, alle angemeldeten Benutzer müssen sich danach erneut anmelden.
 *
 * @seit 2.6.0
 */
define('AUTH_KEY',         'F10:t@I&B<Ee%9KkOoicXYAS>AZA+!ha.`T-&}.p)CfkGdm-Kd9IN>XrM9`VD3l>');
define('SECURE_AUTH_KEY',  '.xtijXFB{yWjL4G//Uk8SV9xO% A!E5ga@7rRWg[|a#=je<Ot2O3h|(xEbqgK.n,');
define('LOGGED_IN_KEY',    '^PBJ`Sii&DxB!#bnN2k)%N!pUW]^8`wyJ3$;eLvH/ia{~CR30QAE$nkpg,zS1kV(');
define('NONCE_KEY',        '$UtF]nEGMO_L1KaQ(#rwI=Sc}aR+.;!/]VSs@WLWq6}FsXi<pF]h</{zP)AileiH');
define('AUTH_SALT',        'PT{# `HPtOK.:[!;f{74}w^z}9ny8;5.Je}=K9iDhL.(EtT*NyQ!~`-$Oj^TpS,U');
define('SECURE_AUTH_SALT', '8=6*B9bda>-2hznI9LW5idrc,P;{+V[N5IB/RR+%^c2GDI448xU@qG2~f6B;354Q');
define('LOGGED_IN_SALT',   '~?64! )/=c$W-D,uprDv}sh_`?ko*]B`w-:>Kz{2VAzu_pl0vRA0#GheN##jn-%K');
define('NONCE_SALT',       'GJfcG7F~X#(_(E  0]nmiYAoR(|`2+b_&A7C?#rHtk!PV80U:&O>u<lH,6bC4~R ');

/**#@-*/

/**
 * WordPress Datenbanktabellen-Präfix
 *
 *  Wenn du verschiedene Präfixe benutzt, kannst du innerhalb einer Datenbank
 *  verschiedene WordPress-Installationen betreiben. Nur Zahlen, Buchstaben und Unterstriche bitte!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Sprachdatei
 *
 * Hier kannst du einstellen, welche Sprachdatei benutzt werden soll. Die entsprechende
 * Sprachdatei muss im Ordner wp-content/languages vorhanden sein, beispielsweise de_DE.mo
 * Wenn du nichts einträgst, wird Englisch genommen.
 */
define('WPLANG', 'de_DE');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');