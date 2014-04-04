<?php
/**
* LDAP Settings
*
*/
        $config['LDAP']['Db']['Config'] = 'ldap'; //What is the name of the db config that has the LDAP credentials
        $config['LDAP']['User']['Identifier'] = 'uid'; //What is the LDAP attribute that identifies the username attribute,
                                                       // openldap, iplant, netscapr use uid, AD uses samaccountname
        $config['LDAP']['Group']['Identifier'] = 'cn'; //What is the LDAP attribute that identifies the group name, usually cn
        $config['LDAP']['Model'] = 'Idbroker.LdapAuth'; //Default model to use for LDAP components
        $config['LDAP']['LdapAuth']['Model'] = 'Idbroker.LdapAuth';
        $config['LDAP']['LdapAuth']['MirrorSQL']['Users'] = 'User'; //A SQL table to duplicate ldap records in for user
        $config['LDAP']['LdapAuth']['MirrorSQL']['Groups'] = 'Group'; //A SQL table to duplicate LDAP records in for groups
        $config['LDAP']['LdapACL']['Model'] = 'Idbroker.LdapAcl';
        $config['LDAP']['LdapACL']['groupType'] = 'group';
        $config['LDAP']['groupType'] = 'groupofuniquenames'; //What object class do you use for your groups?
        $config['LDAP']['Group']['behavior']['tree']['parent_id'] = 'XXX'; //Are you using a tree behavior?  Need to set the default parent_id?
?>