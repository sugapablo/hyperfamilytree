-- MySQL dump 9.10
--
-- ------------------------------------------------------
-- Server version	4.0.11a-gamma

--
-- Table structure for table `phpft_admin`
--

CREATE TABLE phpft_admin (
  id smallint(6) NOT NULL auto_increment,
  username varchar(12) NOT NULL default '',
  email varchar(40) NOT NULL default '',
  supreme tinyint(1) NOT NULL default '0',
  permissions tinyint(4) NOT NULL default '0',
  person smallint(6) NOT NULL default '0',
  password varchar(12) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table `phpft_people`
--

CREATE TABLE phpft_people (
  id smallint(6) NOT NULL auto_increment,
  first_name varchar(20) NOT NULL default '',
  middle_name varchar(20) NOT NULL default '',
  last_name varchar(30) NOT NULL default '',
  maiden_name varchar(30) NOT NULL default '',
  gender char(1) NOT NULL default '',
  birthdate date NOT NULL default '0000-00-00',
  died date NOT NULL default '0000-00-00',
  birthplace varchar(40) NOT NULL default '',
  father smallint(6) NOT NULL default '0',
  mother smallint(6) NOT NULL default '0',
  about text NOT NULL,
  adopted tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table `phpft_photos`
--

CREATE TABLE phpft_photos (
  id smallint(6) NOT NULL auto_increment,
  person smallint(6) NOT NULL default '0',
  caption varchar(255) NOT NULL default '',
  year varchar(4) NOT NULL default '',
  filename varchar(40) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table `phpft_siblings`
--

CREATE TABLE phpft_siblings (
  id smallint(6) NOT NULL auto_increment,
  person1 smallint(6) NOT NULL default '0',
  person2 smallint(6) NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table `phpft_spouses`
--

CREATE TABLE phpft_spouses (
  id smallint(6) NOT NULL auto_increment,
  person1 smallint(6) NOT NULL default '0',
  person2 smallint(6) NOT NULL default '0',
  married date NOT NULL default '0000-00-00',
  divorced date NOT NULL default '0000-00-00',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

