SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `mccownwedding`
-- Database structure for maintaining the guests and song requests for the McCown Wedding website.
--

-- --------------------------------------------------------

--
-- Table structure for table `guests`
--

CREATE TABLE IF NOT EXISTS `guests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guest_id` varchar(5) NOT NULL COMMENT 'Unique ID Given to Guest',
  `firstname` varchar(56) NOT NULL COMMENT 'First Name of Guest',
  `lastname` varchar(56) NOT NULL COMMENT 'Last Name of Guest',
  `additional` int(2) NOT NULL DEFAULT '0' COMMENT 'Additional Invites Permitted',
  `additional_confirmed` int(2) NOT NULL DEFAULT '0' COMMENT 'Additional Guests Coming',
  `requests` int(2) NOT NULL DEFAULT '0' COMMENT 'Song Requests Permitted',
  `requests_confirmed` int(2) NOT NULL DEFAULT '0' COMMENT 'Songs Requested by Guest',
  `attending` tinyint(1) NOT NULL COMMENT 'Completed RSVP Form',
  `rsvp_time` datetime NOT NULL COMMENT 'Time RSVP Form Completed',
  PRIMARY KEY (`id`),
  UNIQUE KEY `guest_id` (`guest_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Table structure for table `song_requests`
--

CREATE TABLE IF NOT EXISTS `song_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `requestor` varchar(256) NOT NULL,
  `requestor_id` int(11) NOT NULL,
  `title` varchar(256) NOT NULL,
  `artist` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;
