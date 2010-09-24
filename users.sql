--
-- Table structure for table `osfm_users`
--

DROP TABLE IF EXISTS `osfm_users`;
CREATE TABLE IF NOT EXISTS `osfm_users` (
  `id` int(11) NOT NULL auto_increment,
  `user` varchar(255) NOT NULL default '',
  `pass` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `folder` varchar(255) NOT NULL default '',
  `http` varchar(255) NOT NULL default '',
  `currsess` varchar(25) NOT NULL default '',
  `spacelimit` varchar(255) NOT NULL default '',
  `theme` varchar(255) NOT NULL default '',
  `language` varchar(255) NOT NULL default '',
  `permbrowse` int(1) NOT NULL default '1',
  `permupload` int(1) NOT NULL default '0',
  `permcreate` int(1) NOT NULL default '0',
  `permuser` int(1) NOT NULL default '0',
  `permadmin` int(1) NOT NULL default '0',
  `permdelete` int(1) NOT NULL default '0',
  `permmove` int(1) NOT NULL default '0',
  `permchmod` int(1) NOT NULL default '0',
  `permget` int(1) NOT NULL default '0',
  `permdeleteuser` int(1) NOT NULL default '0',
  `permedituser` int(1) NOT NULL default '0',
  `permmakeuser` int(1) NOT NULL default '0',
  `permpass` int(1) NOT NULL default '0',
  `permedit` int(1) NOT NULL default '0',
  `permrename` int(1) NOT NULL default '0',
  `permsub` int(1) NOT NULL default '0',
  `permrecycle` int(1) NOT NULL default '0',
  `permprefs` int(1) NOT NULL default '1',
  `formatperm` int(1) NOT NULL default '0',
  `status` int(1) NOT NULL default '1',
  `recycle` int(1) NOT NULL default '0',
  KEY `user_id` (`id`)
) TYPE=MyISAM  AUTO_INCREMENT=3 ;

--
-- Dumping data for table `osfm_users`
--

INSERT INTO `osfm_users` VALUES(1, 'admin', '5f4dcc3b5aa765d61d8327deb882cf99', 'you@email.com', 'Admin', './', '', '20081005234859', '999999999999999999999', 'classic', 'english', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, 0);
INSERT INTO `osfm_users` VALUES(2, 'root', '5f4dcc3b5aa765d61d8327deb882cf99', 'you@email.com', 'root', '.', '', '20081005222012', '500000', 'classic', 'english', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, 0);
