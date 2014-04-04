LAUDATIO-Repository
===================

Copyright: © 2014 Computer- and Media Service, Humboldt-Universität zu Berlin
  License: Apache Licence 2.0
   Status: Draft
  Version: 1.0
  Authors: Dennis Zielke, Computer- and Media Service, Humboldt-Universität zu Berlin,
           Tino Schernickau Computer- and Media Service, Humboldt-Universität zu Berlin

The concept of this repository infrastructure software for a particular sub-discipline of linguistics (historical corpus linguistics) is modular. The modules of the repository correspond to the application features presentation, storage, indexing and retrieval, as well as management of Persistent Identifiers (PID). 

The LAUDATIO-Repository is available here:
http://www.laudatio-repository.org

Used software tools
===================
  Fedora Commons
  --------------
  Version 3.6
  
  <b>Installation</b><br>
  apt-get install apache2
  apt-get install php5, php5-mysql, php5-gd, php5-curl, php5-xsl
  apt-get install mysql-server
  apt-get install tomcat6
  apt-get install imagemagick
  
  <b>Requirements</b><br>
  Download Java JDK 6
  http://www.oracle.com/technetwork/java/javase/downloads/jdk-6u27-download-440405.html
  Installing Java JDK 6
  Changing Enviroment Variables FEDORA_HOME - C:fedora PATH - %FEDORA_HOME%serverbin;%FEDORA_HOME%clientbin;%JAVA_HOME%bin

  <b>Firewall settings</b><br>
  ufw default deny
  ufw allow ssh
  ufw status
  ufw allow proto tcp from any to any port 80
  ufw allow proto tcp from any to any port 8080
  ufw enable
  
  Please follow the instructions of custom installation
  ...
  
  <b>MySQL database</b><br>
  Please note that the MySQL JDBC driver provided by the installer requires MySQL v3.23.x or higher.
  The MySQL commands listed below can be run within the mysql program, which may be invoked as follows:
  mysql -u root -p
  Create the database. For example, to create a database named “fedora3”, enter:
  CREATE DATABASE fedora3;
  Set username, password and permissions for the database. For example, to set the permissions for user fedoraAdmin with password fedoraAdmin on database “fedora3”, enter:
  GRANT ALL ON fedora3.* TO XXX@localhost IDENTIFIED BY ‘XXX’;
  GRANT ALL ON fedora3.* TO XXX@’%’ IDENTIFIED BY ‘XXX’;
  
  
  <b>REST-API</b><br>
  We use fedoras REST API via CURL requests to store and access the teiHeaders and some other Files. 
  Additionally fedora can be administrated via the Fedora Web Administrator e.g. at http://localhost:8080/fedora/admin/.
  Further information you can find here:
  https://wiki.duraspace.org/display/FEDORA34/Fedora+Web+Administrator
  
  Further details e.g. to fedora data model or which files we are stored in fedora can you find at here:
  http://www.laudatio-repository.org/repository/technical-documentation/software/fedora.html#files

  Graphical User Interface with CakePHP
  -------------------------------------
  
    We are use CakePHP, a PHP MVC Framework, in Version 2.4.5
    For Installation- and requirementdetails see here:
    http://book.cakephp.org/2.0/en/installation.html
    
    Model/Controller
  
    <b>XMLObject</b><br>
    This is the main model of the laudatio-repository website. It represents the teiHeader by providing methods to manage the data stored in Fedora Commons.
    
    <b>User</b><br>
    User, Group, LDAP, CorpusCreator Controllers and Models are used to manage access rights to modify, view or delete corpora.
    
    <b>Configuration</b><br>
    The Configuration Controller provides methods for the repository administrators. That includes the configuration of the used scheme for uploading corpora, the view configuration, a form to upload new schemes, buttons to reindex all corpora in elasticsearch and to update all handle pids.


  ElasticSearch
  -------------
  Version 0.9
  
  Installationdetails can you find here:

  <b>API</b><br> 
  The elastic search API is online available at http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/index.html

  <b>Testing with elasticsearch HEAD</b><br>
  elasticsearch HEAD tool is online available at https://github.com/mobz/elasticsearch-head. It’s a web front end for browsing and interacting with elasticsearch.

  <b>Indexing</b><br>
  The teiHeaders are xml files, but ealasticsearch uses JSON as data format. We use Java Bridge, a tomcat webapp, to convert the xml files to json.
  The Java Bridge uses the XSLT Processor `SAXON <http://saxon.sourceforge.net/>’_ to convert the xml files. The xsl-file is located at /var/www/xsltjson/conf/. 
  Further details to indexing and mapping find here:
  http://www.laudatio-repository.org/repository/technical-documentation/software/elasticsearch.html

  PID-system (handle)
  -------------------
  
  <b>API</b><br>
  We are used the API provided by EUROPEAN Persistent Identifier Consortium (EPIC) in Version 2.
  Download and Installation details are here:
  https://github.com/CatchPlus/EPIC-API-v2/wiki/Core-API
  
  Handles are created via POST request to http://pid.gwdg.de/handles/11022.
  The post data has the following structure:
      [{"type":"URL","parsed_data":"'.$objectURL.'","encoding":"xml"}]
  
  Handle updates require a PUT request to http://pid.gwdg.de/handles/11022/”.$handle with the following data:
      '[{"type":"URL","parsed_data":"'.$objectURL.'"}]'
  
  For handle deletion a DELETE request to http://pid.gwdg.de/handles/11022/”.$handlePID is sufficient.


