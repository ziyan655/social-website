

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

DROP DATABASE `TODO`;
CREATE DATABASE `TODO` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `TODO`;

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `add_concert_in_list`$$
CREATE DEFINER=`TODO`@`TODO` PROCEDURE `add_concert_in_list`(
	IN p_userId INT,
	IN p_listId				INT,
	IN concert_name_string	VARCHAR(4000)
)
BEGIN
	DECLARE lv_list_id		INT(10);
	DECLARE lv_concert_name	VARCHAR(50);
    DECLARE lv_comma_index  INT(10);
	DECLARE lv_concert_id	INT(10);
	-- Inserting concertId corresponding to the list  
		BEGIN 
			 DECLARE EXIT HANDLER FOR SQLEXCEPTION
			 SELECT 'Error in processing Concert List mapping' AS 'ERROR';
			 
			 concert_loop: LOOP
				 SELECT  SUBSTRING_INDEX(concert_name_string, ',', 1) INTO lv_concert_name;
				 SELECT SUBSTRING(concert_name_string FROM INSTR(concert_name_string,',')+1) INTO concert_name_string;
				 SELECT   INSTR(concert_name_string,',') INTO lv_comma_index;
			 
				 -- Fetching Concert Id based on Concert Name
				 BEGIN 
					DECLARE EXIT HANDLER FOR SQLEXCEPTION
						SELECT 'Error in extracting concertId from Concert table ' AS 'ERROR';
					SELECT 
						concertId 
					INTO 
						lv_concert_id
					FROM 
						Concert
					WHERE 
						concertName =lv_concert_name;
					
					BEGIN 
						DECLARE EXIT HANDLER FOR SQLEXCEPTION
						SELECT 'Error in Inserting Data into RECOMMEND_LIST_CONCERT ' AS 'ERROR';
						BEGIN 
							SELECT CONCAT('Concert Id-',lv_concert_id) AS 'SUCCESS_MESSAGE';
							INSERT INTO Recommend_List_Concert VALUES(p_listId,lv_concert_id);
							UPDATE User SET trustScore = trustScore + 2 WHERE userId = p_userId;
							 SELECT CONCAT('Mapping Created for Concert Id:',lv_concert_id) AS 'SUCCESS_MESSAGE';
						 END;
					END;
				 END;
				 IF lv_comma_index=0 THEN
					 LEAVE concert_loop;
				 END IF;
			 END LOOP;
		END;
END$$

DROP PROCEDURE IF EXISTS `create_concert_list`$$
CREATE DEFINER=`TODO`@`TODO` PROCEDURE `create_concert_list`(
	IN p_userId				INT,
	IN p_listName			VARCHAR(50),
	IN concert_name_string	VARCHAR(4000) 
)
BEGIN
	DECLARE lv_list_id		INT(10);
	DECLARE lv_concert_name	VARCHAR(50);
    DECLARE lv_comma_index  INT(10);
	DECLARE lv_concert_id	INT(10);
	BEGIN 
		DECLARE EXIT HANDLER FOR SQLEXCEPTION
			SELECT 'Error in Inserting Data into USER_RECOMMEND_LIST ' AS 'ERROR';
		
		-- Creating List corresponding to the User 
		INSERT INTO  USer_Recommend_List 
			(	
				userId,
				listname,
				creationDate
			)
		VALUES(	
				p_userId,
				p_listName,
				current_timestamp
			);

		SELECT LAST_INSERT_ID() INTO lv_list_id;
	
		-- Inserting concertId corresponding to the list  
		BEGIN 
			 DECLARE EXIT HANDLER FOR SQLEXCEPTION
			 SELECT 'Error in processing Concert List mapping' AS 'ERROR';
			 
			 concert_loop: LOOP
				 SELECT  SUBSTRING_INDEX(concert_name_string, ',', 1) INTO lv_concert_name;
				 SELECT SUBSTRING(concert_name_string FROM INSTR(concert_name_string,',')+1) INTO concert_name_string;
				 SELECT   INSTR(concert_name_string,',') INTO lv_comma_index;
			 
				 -- Fetching Concert Id based on Concert Name
				 BEGIN 
					DECLARE EXIT HANDLER FOR SQLEXCEPTION
						SELECT 'Error in extracting concertId from Concert table ' AS 'ERROR';
					SELECT 
						concertId 
					INTO 
						lv_concert_id
					FROM 
						Concert
					WHERE 
						concertName =lv_concert_name;
					
					BEGIN 
						DECLARE EXIT HANDLER FOR SQLEXCEPTION
						SELECT 'Error in Inserting Data into RECOMMEND_LIST_CONCERT ' AS 'ERROR';
						BEGIN 
							SELECT CONCAT('Concert Id-',lv_concert_id) AS 'SUCCESS_MESSAGE';
							INSERT INTO Recommend_List_Concert VALUES(lv_list_id,lv_concert_id);
							 SELECT CONCAT('Mapping Created for Concert Id:',lv_concert_id) AS 'SUCCESS_MESSAGE';
						 END;
					END;
				 END;
				 IF lv_comma_index=0 THEN
					 LEAVE concert_loop;
				 END IF;
			 END LOOP;
		END;
	END;
END$$

DROP PROCEDURE IF EXISTS `delete_all_lists`$$
CREATE DEFINER=`TODO`@`TODO` PROCEDURE `delete_all_lists`(
	IN p_userId				INT
)
BEGIN
	DECLARE lv_list_id		INT(10);
	DECLARE lv_concert_name	VARCHAR(50);
    DECLARE lv_comma_index  INT(10);
	DECLARE lv_concert_id	INT(10);
	-- Inserting concertId corresponding to the list  
		BEGIN 
			 DECLARE EXIT HANDLER FOR SQLEXCEPTION
			 SELECT 'Error in deleting records from RECOMMEND_LIST_CONCERT' AS 'ERROR';
			 DELETE 
			 FROM 
				Recommend_List_Concert 
			WHERE 
				listId IN (SELECT 
								listId 
							FROM 
								USer_Recommend_List 
							WHERE 
								userId=p_userId
							);
			BEGIN
				DECLARE EXIT HANDLER FOR SQLEXCEPTION
					SELECT 'Error in deleting records from USER_RECOMMEND_LIST' AS 'ERROR';
				DELETE FROM 
					USer_Recommend_List
				WHERE 
					userId=p_userId;
				
				SELECT CONCAT('Deleted all the lists corresponding to the UserId- ',p_userId) AS 'SUCCESS';			
			END;
		END;
END$$

DROP PROCEDURE IF EXISTS `delete_concert_in_list`$$
CREATE DEFINER=`TODO`@`TODO` PROCEDURE `delete_concert_in_list`(
	IN p_listId				INT,
	IN concert_name_string	VARCHAR(4000)
)
BEGIN
	DECLARE lv_list_id		INT(10);
	DECLARE lv_concert_name	VARCHAR(50);
    DECLARE lv_comma_index  INT(10);
	DECLARE lv_concert_id	INT(10);
	-- Inserting concertId corresponding to the list  
		BEGIN 
			 DECLARE EXIT HANDLER FOR SQLEXCEPTION
			 SELECT 'Error in processing Concert List mapping' AS 'ERROR';
			 
			 concert_loop: LOOP
				 SELECT  SUBSTRING_INDEX(concert_name_string, ',', 1) INTO lv_concert_name;
				 SELECT SUBSTRING(concert_name_string FROM INSTR(concert_name_string,',')+1) INTO concert_name_string;
				 SELECT   INSTR(concert_name_string,',') INTO lv_comma_index;
			 
				 -- Fetching Concert Id based on Concert Name
				 BEGIN 
					DECLARE EXIT HANDLER FOR SQLEXCEPTION
						SELECT 'Error in extracting concertId from Concert table ' AS 'ERROR';
					SELECT 
						concertId 
					INTO 
						lv_concert_id
					FROM 
						Concert
					WHERE 
						concertName =lv_concert_name;
					
					BEGIN 
						DECLARE EXIT HANDLER FOR SQLEXCEPTION
						SELECT 'Error in deleting Data from RECOMMEND_LIST_CONCERT ' AS 'ERROR';
						BEGIN 
							SELECT CONCAT('Concert Id-',lv_concert_id) AS 'SUCCESS_MESSAGE';
							DELETE FROM Recommend_List_Concert WHERE listId=p_listId AND concertId=lv_concert_id;
							 SELECT CONCAT('Mapping deleted for Concert Id:',lv_concert_id) AS 'SUCCESS_MESSAGE';
						 END;
					END;
				 END;
				 IF lv_comma_index=0 THEN
					 LEAVE concert_loop;
				 END IF;
			 END LOOP;
		END;
END$$

DROP PROCEDURE IF EXISTS `delete_user_list`$$
CREATE DEFINER=`TODO`@`TODO` PROCEDURE `delete_user_list`(
	IN p_userId				INT,
	IN p_listName			VARCHAR(50)
)
BEGIN
	DECLARE lv_list_id		INT(10);
	-- Fetching List Id 
	BEGIN
		DECLARE EXIT HANDLER FOR SQLEXCEPTION
			SELECT 'Error in extracting listId from user_recommend_list table ' AS 'ERROR';
		DECLARE EXIT handler for not FOUND
			SELECT CONCAT(p_listName,' list does not exist in the system.') AS 'ERROR';
		SELECT 
			listId 
		INTO 
			lv_list_id
		FROM 
			USer_Recommend_List
		WHERE 
			listName =p_listName AND 
			userId=p_userId;
		
		BEGIN 
			DECLARE EXIT HANDLER FOR SQLEXCEPTION
			SELECT 'Error in Deleting Data corrsponding to the List ' AS 'ERROR';
			BEGIN 
				-- Deleting List Concert mapping 
				DELETE FROM Recommend_List_Concert WHERE listId=lv_list_id;
				-- Deleting User List Mapping
				DELETE FROM USer_Recommend_List WHERE listId=lv_list_id AND userId=p_userId;
			 END;
		END;			
	END;
END$$

DROP PROCEDURE IF EXISTS `recommended_Concert`$$
CREATE DEFINER=`TODO`@`TODO` PROCEDURE `recommended_Concert`(IN `p_userName` VARCHAR(30))
    DETERMINISTIC
BEGIN
SELECT
concertName
FROM
Concert cr,
Location l,
User ur
WHERE
cr.overallRating>=4 AND
ur.userName=p_userName AND
ur.cityId=l.cityId AND
l.locationId =cr.locationId AND
0= (SELECT COUNT(*) FROM Concert_Review_Rating WHERE concertId=cr.concertId AND rating < 3) AND
1 <= (SELECT COUNT(DISTINCT url.userId) FROM USer_Recommend_List url,Recommend_List_Concert rlc WHERE url.listId=rlc.listId AND rlc.concertId=cr.concertId AND url.userId<>ur.userId) AND
1 <= (SELECT COUNT(userId) FROM Concert_Attend_User WHERE concertId=cr.concertId);

END$$

DROP PROCEDURE IF EXISTS `User_Artist_fan`$$
CREATE DEFINER=`TODO`@`TODO` PROCEDURE `User_Artist_fan`(
	IN p_userId				INT,
	IN p_artistId			INT
)
BEGIN
	DECLARE lv_user_name VARCHAR(50);
	DECLARE lv_artist_name VARCHAR(50); 
	-- checking User Name 
	BEGIN 
		DECLARE EXIT HANDLER FOR NOT FOUND 
			SELECT 'User doesn not exist in the system.' AS 'ERROR';
		SELECT 
			Name
		INTO 
			lv_user_name
		FROM 
			User 
		WHERE 
			userId=p_userId;
		-- checking follow User Name 
		BEGIN 
			DECLARE EXIT HANDLER FOR NOT FOUND 
				BEGIN 
				SELECT 'This Artist does not exist in the system.' AS 'ERROR';
				END;
			SELECT 
				artistName
			INTO 
				lv_artist_name
			FROM 
				Artist  
			WHERE 
				artistId=p_artistId;
			-- Inserting Data in USER Table.
			BEGIN 
				DECLARE EXIT HANDLER FOR SQLEXCEPTION
					SELECT 'Error in Inserting Data into USER_ARTIST';
				INSERT INTO  User_Artist 
					VALUES(p_userId,p_artistId,current_timestamp);
				SELECT CONCAT(lv_user_name,CONCAT(' is following Artist ',lv_artist_name)) AS 'SUCCESS_MESSAGE';

			END;
		END;
	END;
END$$

DROP PROCEDURE IF EXISTS `User_Band_like`$$
CREATE DEFINER=`TODO`@`TODO` PROCEDURE `User_Band_like`(
	IN p_userId				INT,
	IN p_bandId			INT
)
BEGIN
	DECLARE lv_user_name VARCHAR(50);
	DECLARE lv_band_name VARCHAR(50); 
	-- checking User Name 
	BEGIN 
		DECLARE EXIT HANDLER FOR NOT FOUND 
			SELECT 'User doesn not exist in the system.' AS 'ERROR';
		SELECT 
			Name
		INTO 
			lv_user_name
		FROM 
			User 
		WHERE 
			userId=p_userId;
		-- checking follow User Name 
		BEGIN 
			DECLARE EXIT HANDLER FOR NOT FOUND 
				BEGIN 
				SELECT 'This Band does not exist in the system.' AS 'ERROR';
				END;
			SELECT 
				bandName
			INTO 
				lv_band_name
			FROM 
				Band  
			WHERE 
				bandId=p_bandId;
			-- Inserting Data in USER Table.
			BEGIN 
				DECLARE EXIT HANDLER FOR SQLEXCEPTION
					SELECT 'Error in Inserting Data into USER_BAND';
				INSERT INTO  User_Band 
					VALUES(p_userId,p_bandId,current_timestamp);
				SELECT CONCAT(lv_user_name,CONCAT(' is following Band ',lv_band_name)) AS 'SUCCESS_MESSAGE';

			END;
		END;
	END;
END$$

DROP PROCEDURE IF EXISTS `user_follow`$$
CREATE DEFINER=`TODO`@`TODO` PROCEDURE `user_follow`(
	IN p_userId				INT,
	IN p_user_follow		INT
)
BEGIN
	DECLARE lv_user_name VARCHAR(50);
	DECLARE lv_user_follow_name VARCHAR(50); 
	-- checking User Name 
	BEGIN 
		DECLARE EXIT HANDLER FOR NOT FOUND 
			SELECT 'User doesn not exist in the system.' AS 'ERROR';
		SELECT 
			Name
		INTO 
			lv_user_name
		FROM 
			User 
		WHERE 
			userId=p_userId;
		-- checking follow User Name 
		BEGIN 
			DECLARE EXIT HANDLER FOR NOT FOUND 
				BEGIN 
				SELECT 'User whom this user follows does not exist in the system.' AS 'ERROR';
				END;
			SELECT 
				Name
			INTO 
				lv_user_follow_name
			FROM 
				User 
			WHERE 
				userId=p_user_follow;

			-- Inserting Record in Table 
			BEGIN 
				DECLARE EXIT HANDLER FOR SQLEXCEPTION
					SELECT 'Error in Inserting Data into USER_FOLLOWERS' AS 'ERROR';
				INSERT INTO  User_Followers 
					VALUES(p_userId,p_user_follow,current_timestamp);
				
				SELECT CONCAT(lv_user_name,CONCAT(' is following ',lv_user_follow_name)) AS 'SUCCESS_MESSAGE';
			END;
		END;
	END;
 END$$

DROP PROCEDURE IF EXISTS `User_review_rating`$$
CREATE DEFINER=`TODO`@`TODO` PROCEDURE `User_review_rating`(
	IN p_concertId			INT,
	IN p_userId				INT,
	IN p_reviewDescription	VARCHAR(1000),
	IN p_rating				INT
)
BEGIN
	DECLARE lv_user_name VARCHAR(50);
	DECLARE lv_Concert_name VARCHAR(50); 
	-- checking User Name 
	BEGIN 
		DECLARE EXIT HANDLER FOR NOT FOUND 
			SELECT 'User doesn not exist in the system.' AS 'ERROR';
		SELECT 
			Name
		INTO 
			lv_user_name
		FROM 
			User 
		WHERE 
			userId=p_userId;
		-- checking follow User Name 
		BEGIN 
			DECLARE EXIT HANDLER FOR NOT FOUND 
				BEGIN 
				SELECT 'This Concert does not exist in the system. ' AS 'ERROR';
				END;
			SELECT 
				concertName
			INTO 
				lv_Concert_name
			FROM 
				Concert  
			WHERE 
				concertId=p_concertId;

		  -- Inserting Data in USER Table.
			BEGIN 
				DECLARE EXIT HANDLER FOR SQLEXCEPTION
					SELECT 'Error in Inserting Data into CONCERT_REVIEW_RATING' AS 'ERROR';
				INSERT INTO  Concert_Review_Rating 
					VALUES(p_concertId,p_userId,p_reviewDescription,p_rating,current_timestamp);
				UPDATE User SET trustScore = trustScore + 3 WHERE userId = p_userId;
				SELECT CONCAT(lv_user_name,CONCAT(' has published review and rating for the Concert ',lv_Concert_name)) AS 'SUCCESS_MESSAGE';

			END;
		END;
	END;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `ARTIST_Music_Category`
--

DROP TABLE IF EXISTS `ARTIST_Music_Category`;
CREATE TABLE IF NOT EXISTS `ARTIST_Music_Category` (
  `artistId` int(10) NOT NULL DEFAULT '0',
  `musicCatId` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`artistId`,`musicCatId`),
  KEY `musicCatId` (`musicCatId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ARTIST_Music_Category`
--

INSERT INTO `ARTIST_Music_Category` (`artistId`, `musicCatId`) VALUES
(1, 1),
(2, 1),
(3, 1),
(3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `ARTIST_Music_SubCategory`
--

DROP TABLE IF EXISTS `ARTIST_Music_SubCategory`;
CREATE TABLE IF NOT EXISTS `ARTIST_Music_SubCategory` (
  `artistId` int(10) NOT NULL DEFAULT '0',
  `subCatId` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`artistId`,`subCatId`),
  KEY `subCatId` (`subCatId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ARTIST_Music_SubCategory`
--

INSERT INTO `ARTIST_Music_SubCategory` (`artistId`, `subCatId`) VALUES
(1, 1),
(2, 1),
(4, 1),
(3, 2),
(4, 2),
(2, 3),
(10, 11),
(11, 11),
(6, 12),
(13, 12);

-- --------------------------------------------------------

--
-- Table structure for table `Artist`
--

DROP TABLE IF EXISTS `Artist`;
CREATE TABLE IF NOT EXISTS `Artist` (
  `artistId` int(10) NOT NULL AUTO_INCREMENT,
  `artistName` varchar(30) NOT NULL,
  `userName` varchar(30) NOT NULL,
  `password` varchar(30) NOT NULL,
  `emailid` varchar(100) DEFAULT NULL,
  `description` varchar(400) DEFAULT NULL,
  `bandId` int(10) DEFAULT NULL,
  `websiteLink` varchar(70) DEFAULT NULL,
  `creationDate` datetime DEFAULT '0000-00-00 00:00:00',
  `countryId` int(10) DEFAULT NULL,
  `wall` varchar(400) DEFAULT NULL,
  `url` varchar(70) DEFAULT NULL,
  PRIMARY KEY (`artistId`),
  UNIQUE KEY `userName` (`userName`),
  KEY `bandId` (`bandId`),
  KEY `countryId` (`countryId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `Artist`
--

INSERT INTO `Artist` (`artistId`, `artistName`, `userName`, `password`, `emailid`, `description`, `bandId`, `websiteLink`, `creationDate`, `countryId`, `wall`, `url`) VALUES
(1, 'John Lennon', 'artist', 'artist', 'ee@nyu.ik', 'p', 3, 'www.lm', '2014-12-08 10:21:10', 2, 'Nice today. Holiday man. Wohoo!', ''),
(2, 'Mick Jagger', 'mickjagger', 'mickjagger', 'blink@nyu.edu', 'Very ambitious musician. Will be popular soon in the 21st century.', 2, 'www.therollingstones.com', '2014-12-08 10:21:10', 2, 'So busy with many concerts...', NULL),
(3, 'Pete Townshend', 'petetownshend', 'petetownshend', 'gogoyou@126.com', 'I work hard everyday. no one blames then.', 3, 'www.thewho.com', '2014-12-08 10:21:10', 1, 'feel nice now. just earned some reputation through the latest concert.', NULL),
(4, 'Roger Daltrey', 'rogerdaltrey', 'rogerdaltrey', 'epson@pu.edu', 'He performs well and attract all the audience. No one says positively on him.', 3, 'www.thewho.com', '2014-12-08 10:21:10', 1, 'need rest definitely.. my assistant got sick..', NULL),
(5, 'Dave Hills', 'hill', 'hill', 'hson@pu.edu', 'I never walk backward. I never quit. That is me.', NULL, 'www.linmkage.com', '2014-12-08 10:21:10', 1, 'just met Linda, she''s been really helpful to me. Thanks!', NULL),
(6, 'Linda Mu', 'linda', 'linda', 'linhson@pu.edu', 'Be happy everyday! forever!', NULL, 'www.linda.com', '2014-12-08 10:21:10', 1, 'I need to practice giving autography now...    ne one recognizes my handwriting..', NULL),
(7, 'Lew Dun', 'lew', 'lew', 'linhson@pu.edu', 'I am a Jazz expert. No one defeats me.', 5, 'www.jazzda.com', '2014-12-08 10:21:10', 1, 'Jazz is so good. Hope devote to this for my entire life.', NULL),
(8, 'Token Li', 'token', 'token', 'tokson@pu.edu', 'I am not a entertainer. I am a musician.', 10, 'www.muzda.com', '2014-12-08 10:21:10', 1, 'Too much fund raising work to do. Feel exhausted!', NULL),
(9, 'Hingu Moris', 'hingu', 'hingu', 'tofkson@pu.edu', 'I get paid. Then I perform well.But if not paid well, I still perform well... you see the logic here?', 1, 'www.m1uzda.com', '2014-12-08 10:21:10', 1, 'Can any artist try to cooperate a piano song with me? need to show mom.', NULL),
(10, 'You Kim', 'you', 'you', 'you@nyu.edu', 'People say I am a perfectionist. But i regard myself as Father of Hip Pop. Today, no one knows the nature of Hip Pop. So sad though.', 7, 'www.pop.com', '2014-12-08 10:21:10', 1, 'I will show what is hip pop in few days in a concert. come join!', NULL),
(11, 'Jay Tooni', 'jay', 'jay', 'jay@nyu.edu', 'I dance and sing. If not sing, I can''t dance well. I hope I will become a Godfather of dance in the future.', 1, 'www.pdancep.com', '2014-12-08 10:21:10', 1, 'I just invented a new moon walk style. Check it out in the concert The Moon.', NULL),
(12, 'Final Kik', 'final', 'final', 'jay@nyu.edu', 'I dance and sing. If not sing, I can''t dance well. I hope I will become a Godfather of dance in the future.', 8, 'www.pdancep.com', '2014-12-08 10:21:10', 3, 'I just invented a new moon walk style. Check it out in the concert The Moon.', NULL),
(13, 'JJ', 'jj', 'jj', 'jj@nyu.edu', 'I am cool', 4, 'www.google.com', '2014-12-08 16:37:21', 2, 'I am new!', NULL),
(14, 'Li Jian', 'lj', 'lj', 'YekDuy@duke.edu', 'Good. Well Thanks!', 3, NULL, '0000-00-00 00:00:00', NULL, 'Wat!? But thanks!', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `Artist_Concert`
--

DROP TABLE IF EXISTS `Artist_Concert`;
CREATE TABLE IF NOT EXISTS `Artist_Concert` (
  `artistId` int(10) NOT NULL DEFAULT '0',
  `concertId` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`artistId`,`concertId`),
  KEY `concertId` (`concertId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Artist_Concert`
--

INSERT INTO `Artist_Concert` (`artistId`, `concertId`) VALUES
(2, 2),
(3, 3),
(4, 3),
(5, 15),
(1, 24),
(1, 25),
(1, 26),
(14, 27);

-- --------------------------------------------------------

--
-- Table structure for table `Band`
--

DROP TABLE IF EXISTS `Band`;
CREATE TABLE IF NOT EXISTS `Band` (
  `bandId` int(10) NOT NULL AUTO_INCREMENT,
  `bandname` varchar(30) NOT NULL,
  `description` varchar(400) DEFAULT NULL,
  `creationDate` datetime DEFAULT '0000-00-00 00:00:00',
  `url` varchar(70) DEFAULT NULL,
  PRIMARY KEY (`bandId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `Band`
--

INSERT INTO `Band` (`bandId`, `bandname`, `description`, `creationDate`, `url`) VALUES
(1, 'The Beatles', ' The Beatles were unchallenged by even anyone. ', '2014-12-08 10:21:09', NULL),
(2, 'The Rolling Stones', ' The Rolling Stones as the best rock band ever', '2014-12-08 10:21:10', NULL),
(3, 'The Who', 'Wat?!', '2014-12-08 10:21:10', 'aaa.ccom'),
(4, 'Fire', 'A band that is on fire!', '2014-12-08 10:21:10', NULL),
(5, 'Highwater', 'We walk on water. Sing on water.', '2014-12-08 10:21:10', NULL),
(6, 'Little Bee', 'Folow your heart. Never quit!', '2014-12-08 10:21:10', NULL),
(7, 'Koo Yo', 'No one regrets after liking us! Just do it!', '2014-12-08 10:21:10', NULL),
(8, 'Lakoa', 'Just do it! I have a dream to sing at all places.', '2014-12-08 10:21:10', NULL),
(9, 'Danking', 'Dance and sing. Sing and dance. Our band is eternal.', '2014-12-08 10:21:10', NULL),
(10, 'Emotion', 'Ready to control your emotion? You should focus on us. Emotioin band!', '2014-12-08 10:21:10', NULL),
(11, 'Ins', 'Insdutry Talents! Thanks', '2014-12-08 10:21:10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `Band_M_Cat`
--

DROP TABLE IF EXISTS `Band_M_Cat`;
CREATE TABLE IF NOT EXISTS `Band_M_Cat` (
  `bandId` int(10) NOT NULL DEFAULT '0',
  `musicCatId` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`bandId`,`musicCatId`),
  KEY `musicCatId` (`musicCatId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Band_M_Cat`
--

INSERT INTO `Band_M_Cat` (`bandId`, `musicCatId`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(11, 1),
(5, 2),
(6, 3),
(7, 3),
(9, 3),
(10, 3);

-- --------------------------------------------------------

--
-- Table structure for table `Band_M_Sub`
--

DROP TABLE IF EXISTS `Band_M_Sub`;
CREATE TABLE IF NOT EXISTS `Band_M_Sub` (
  `bandId` int(10) NOT NULL DEFAULT '0',
  `subCatId` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`bandId`,`subCatId`),
  KEY `subCatId` (`subCatId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Band_M_Sub`
--

INSERT INTO `Band_M_Sub` (`bandId`, `subCatId`) VALUES
(1, 1),
(2, 3),
(7, 4),
(6, 5),
(9, 5),
(5, 6);

-- --------------------------------------------------------

--
-- Table structure for table `City`
--

DROP TABLE IF EXISTS `City`;
CREATE TABLE IF NOT EXISTS `City` (
  `cityId` int(10) NOT NULL AUTO_INCREMENT,
  `cityName` varchar(30) NOT NULL,
  `countryId` int(10) DEFAULT NULL,
  PRIMARY KEY (`cityId`),
  KEY `countryId` (`countryId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;

--
-- Dumping data for table `City`
--

INSERT INTO `City` (`cityId`, `cityName`, `countryId`) VALUES
(1, 'Sydney', 1),
(2, 'Melbourne', 1),
(3, 'New York', 2),
(4, 'Los Angeles', 2),
(5, 'Boston', 2),
(7, 'San Francesco', 2),
(8, 'Lafayette', 2),
(9, 'Denver', 2),
(10, 'San Diego', 2),
(11, 'Chicago', 2),
(12, 'Seattle', 2),
(13, 'Brooklyn', 2),
(14, 'Miami', 2),
(15, 'Keywest', 2),
(20, 'Vancuver', 3),
(21, 'Wottawa', 3),
(23, 'New Jersey', 2),
(24, 'Manhattan', 2),
(25, 'Long Island City', 3);

-- --------------------------------------------------------

--
-- Table structure for table `Concert`
--

DROP TABLE IF EXISTS `Concert`;
CREATE TABLE IF NOT EXISTS `Concert` (
  `concertId` int(10) NOT NULL AUTO_INCREMENT,
  `concertName` varchar(50) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `locationId` int(10) DEFAULT NULL,
  `eventDate` date NOT NULL,
  `eventTime` time NOT NULL,
  `ticketPrice` float DEFAULT '0',
  `availableSeats` int(10) NOT NULL,
  `bookingSiteLink` varchar(50) DEFAULT NULL,
  `overallRating` int(10) DEFAULT NULL,
  `createdByType` varchar(50) DEFAULT NULL,
  `createdBy` int(10) DEFAULT NULL,
  `creationDate` datetime DEFAULT '0000-00-00 00:00:00',
  `bandId` int(10) DEFAULT NULL,
  PRIMARY KEY (`concertId`),
  KEY `bandId` (`bandId`),
  KEY `locationId` (`locationId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=34 ;

--
-- Dumping data for table `Concert`
--

INSERT INTO `Concert` (`concertId`, `concertName`, `description`, `locationId`, `eventDate`, `eventTime`, `ticketPrice`, `availableSeats`, `bookingSiteLink`, `overallRating`, `createdByType`, `createdBy`, `creationDate`, `bandId`) VALUES
(1, 'The Moon', 'Light with the moon. Sleep deep.', 1, '2016-04-27', '01:38:58', 1500, 100, 'www.booktickets.com', 6, 'BAND', 3, '2014-12-08 10:21:18', 3),
(2, 'First Son', 'My first son. It is related to a true story. No one ever know.', 2, '2016-01-09', '01:38:58', 1100, 100, 'www.booktickets.com', 5, 'BAND', 3, '2014-12-08 10:21:18', 1),
(3, 'Cat', 'Cat in LA. Come join. No regrets!', 3, '2014-12-16', '01:38:58', 2500, 200, 'www.booktickets.com', 8, 'BAND', 1, '2014-12-08 10:21:19', 2),
(4, 'Walker', 'Walk on my freedom. It is just about time to dream away to keep my pace slowly.', 4, '2014-12-15', '08:00:58', 1520, 100, 'www.booktickets.com', 9, 'BAND', 2, '2014-12-08 10:21:19', 2),
(5, 'The Italian', 'Italian style Jazz. Very popular in Scisily. A must come concert.', 7, '2016-02-04', '08:00:58', 1520, 100, 'www.booktickets.com', 3, 'BAND', 2, '2014-12-08 10:21:19', 2),
(6, 'Greek Fashion', 'Mideastern style music. Very exciting lyrics. Bring you to a Carriben night.', 2, '2014-12-29', '07:00:58', 1520, 100, 'www.booktickets.com', 6, 'BAND', 2, '2014-12-08 10:21:19', 2),
(7, 'Beetis', 'A yound woman who devote herself to Light music. She will shock the audience. Join us on Friday.', 5, '2014-12-12', '07:00:58', 1520, 100, 'www.booktickets.com', 9, 'BAND', 2, '2014-12-08 10:21:19', 2),
(8, 'King', 'Blues style in the jungle. Join us on Thursday night. There will be Justin showing up!', 6, '2014-12-14', '17:00:58', 1520, 102, 'www.booktickets.com', 7, 'BAND', 2, '2014-12-08 10:21:19', 3),
(9, 'River Within', 'Just join us! Nice mood concert. Best place to relax and dream.', 3, '2014-12-10', '17:00:58', 1520, 102, 'www.booktickets.com', 7, 'BAND', 2, '2014-12-08 10:21:19', 3),
(10, 'Soul ', 'Deep sorrow into the heart. Very sad music oncert. Feel sorrow here! Come and join us! Also availabl', 3, '2014-12-25', '18:00:58', 1520, 102, 'www.booktickets.com', 7, 'BAND', 2, '2014-12-08 10:21:19', 2),
(15, 'Yolo', 'Last concert. Please come.', 5, '2015-03-27', '14:22:00', 200, 278, '', 10, 'BAND', 1, '2015-03-19 14:57:24', NULL),
(20, 'Moris', 'Comibines suspense and excitement. Worth coming!', 5, '2015-04-03', '14:22:00', 233, 2, '', 3, 'BAND', 1, '2015-03-28 00:22:33', 3),
(24, 'My Own', 'Love story based on Linda and David.', 5, '2016-06-30', '14:12:00', 234, 11, '', 5, 'BAND', 1, '2015-03-28 01:01:34', NULL),
(25, 'Join Up', 'Must please you', 5, '2016-04-11', '14:22:00', 22, 2341, '', 8, 'BAND', 1, '2015-03-28 23:24:06', NULL),
(26, 'The Light', 'Light directs me to heaven.', 5, '2016-03-03', '11:11:00', 677, 599, '', 0, 'BAND', 1, '2015-04-03 17:28:13', NULL),
(27, 'KuiYou', 'The culture of absolute understanding starts here. Best concert guaranteed.', 5, '2015-06-26', '14:22:00', 222, 22, '', 0, 'BAND', 1, '2015-04-04 00:05:38', NULL),
(31, 'In the dark', 'Explores people''s heart in the dark,', 5, '2016-06-15', '14:22:00', 222, 22, '', 0, 'BAND', 1, '2015-04-04 00:05:38', NULL),
(32, 'Leap to the future', 'See the future as an old man..', 6, '2015-05-14', '17:00:58', 1520, 102, 'www.booktickets.com', 7, 'BAND', 2, '2014-12-08 10:21:19', 3);

-- --------------------------------------------------------

--
-- Table structure for table `Concert_Attend_User`
--

DROP TABLE IF EXISTS `Concert_Attend_User`;
CREATE TABLE IF NOT EXISTS `Concert_Attend_User` (
  `concertId` int(10) NOT NULL DEFAULT '0',
  `userId` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`concertId`,`userId`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Concert_Attend_User`
--

INSERT INTO `Concert_Attend_User` (`concertId`, `userId`) VALUES
(1, 1),
(3, 1),
(4, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(15, 1),
(20, 1),
(26, 1),
(1, 2),
(2, 2),
(3, 3),
(3, 4),
(2, 5),
(7, 12),
(2, 21);

-- --------------------------------------------------------

--
-- Table structure for table `Concert_Review_Rating`
--

DROP TABLE IF EXISTS `Concert_Review_Rating`;
CREATE TABLE IF NOT EXISTS `Concert_Review_Rating` (
  `concertId` int(10) NOT NULL DEFAULT '0',
  `userId` int(10) NOT NULL DEFAULT '0',
  `reviewDescription` varchar(1000) DEFAULT NULL,
  `rating` int(10) DEFAULT NULL,
  `creationDate` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`concertId`,`userId`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Concert_Review_Rating`
--

INSERT INTO `Concert_Review_Rating` (`concertId`, `userId`, `reviewDescription`, `rating`, `creationDate`) VALUES
(1, 1, 'Good one!', 7, '2015-03-28 13:10:04'),
(1, 5, 'Good one!', 7, '2014-12-08 15:38:29'),
(1, 16, 'Awful!', 2, '2014-12-09 19:34:42'),
(1, 17, 'Her debut is fantastic. Though advertisement was not good enough.', 3, '2014-12-10 00:15:00'),
(1, 21, 'Does not match his style. But good concert anyway.', 6, '2015-04-19 14:42:47'),
(2, 1, 'It is not easy to describe Alt J sound, and yet something about them has always felt familiar. ', 4, '2014-12-25 00:00:00'),
(2, 2, 'It is not easy to describe Alt J sound, and yet something about them has always felt familiar. ', 3, '2014-12-25 00:00:00'),
(3, 3, 'Hard to understand. But feels awesome.', 2, '2014-12-25 00:00:00'),
(3, 4, 'It is not easy to describe Alt J sound, and yet something about them has always felt familiar. ', 5, '2014-12-25 00:00:00'),
(9, 1, 'Should come earlier!!!!!!', 9, '2015-04-03 20:55:35'),
(24, 1, 'This one not so good. Keep going..', 4, '2015-03-28 23:25:24'),
(24, 3, 'Maybe not next time..', 8, '2015-04-01 04:08:09');

-- --------------------------------------------------------

--
-- Table structure for table `Country`
--

DROP TABLE IF EXISTS `Country`;
CREATE TABLE IF NOT EXISTS `Country` (
  `countryId` int(10) NOT NULL AUTO_INCREMENT,
  `countryName` varchar(30) NOT NULL,
  PRIMARY KEY (`countryId`),
  UNIQUE KEY `countryName` (`countryName`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `Country`
--

INSERT INTO `Country` (`countryId`, `countryName`) VALUES
(1, 'Australia'),
(6, 'Britain'),
(3, 'Canada'),
(4, 'China'),
(5, 'India'),
(7, 'Japan'),
(9, 'Korea'),
(10, 'Mexico'),
(8, 'Russia'),
(2, 'United States');

-- --------------------------------------------------------

--
-- Table structure for table `Location`
--

DROP TABLE IF EXISTS `Location`;
CREATE TABLE IF NOT EXISTS `Location` (
  `locationId` int(10) NOT NULL AUTO_INCREMENT,
  `locationName` varchar(50) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `cityId` int(10) DEFAULT NULL,
  PRIMARY KEY (`locationId`),
  KEY `cityId` (`cityId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `Location`
--

INSERT INTO `Location` (`locationId`, `locationName`, `description`, `cityId`) VALUES
(1, 'The Anan Hotel', '5 Star hotel', 1),
(2, 'Eva Hotel', '351 Brunswick Street, Fitzroy', 2),
(3, 'Carnegie Hall', '881 7th Ave', 3),
(4, 'The Music Center', '135 N Grand Ave', 4),
(5, 'NYU Kimmel', 'NYU', 1),
(6, 'Barclay Center', 'Located in Brooklyn', 5),
(7, 'Million Park', 'A park where many concerts have been held.', 14);

-- --------------------------------------------------------

--
-- Table structure for table `Music_Category`
--

DROP TABLE IF EXISTS `Music_Category`;
CREATE TABLE IF NOT EXISTS `Music_Category` (
  `musicCatId` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`musicCatId`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `Music_Category`
--

INSERT INTO `Music_Category` (`musicCatId`, `name`) VALUES
(2, 'Blues'),
(3, 'Hip Hop'),
(1, 'Jazz'),
(4, 'Piano'),
(5, 'Rock');

-- --------------------------------------------------------

--
-- Table structure for table `Music_SubCategory`
--

DROP TABLE IF EXISTS `Music_SubCategory`;
CREATE TABLE IF NOT EXISTS `Music_SubCategory` (
  `subCatId` int(10) NOT NULL AUTO_INCREMENT,
  `subCatName` varchar(30) NOT NULL,
  `musicCatId` int(10) DEFAULT NULL,
  PRIMARY KEY (`subCatId`),
  KEY `musicCatId` (`musicCatId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `Music_SubCategory`
--

INSERT INTO `Music_SubCategory` (`subCatId`, `subCatName`, `musicCatId`) VALUES
(1, 'Free Jazz', 1),
(2, 'Bebob', 1),
(3, 'Cool Jazz', 1),
(4, 'East Coast hip hop', 3),
(5, 'Hardcore hip hop', 3),
(6, 'Cool Blues', 2),
(7, 'Light Blues', 2),
(8, 'Hard Pop', 3),
(9, 'Heaven Pop', 3),
(10, 'Lightning Piano', 4),
(11, 'Raw Piano', 4),
(12, 'Flushing Rock', 5),
(13, 'Base Rock', 5);

-- --------------------------------------------------------

--
-- Table structure for table `Recommend_List_Concert`
--

DROP TABLE IF EXISTS `Recommend_List_Concert`;
CREATE TABLE IF NOT EXISTS `Recommend_List_Concert` (
  `listId` int(10) NOT NULL DEFAULT '0',
  `concertId` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`listId`,`concertId`),
  KEY `concertId` (`concertId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Recommend_List_Concert`
--

INSERT INTO `Recommend_List_Concert` (`listId`, `concertId`) VALUES
(6, 1),
(8, 1),
(93, 1),
(96, 1),
(6, 2),
(6, 3),
(93, 3),
(95, 4),
(96, 4),
(93, 5),
(3, 7),
(93, 8),
(94, 8),
(97, 9),
(95, 15),
(97, 15),
(93, 20),
(95, 24),
(97, 25),
(95, 26),
(97, 31);

-- --------------------------------------------------------

--
-- Table structure for table `USer_Recommend_List`
--

DROP TABLE IF EXISTS `USer_Recommend_List`;
CREATE TABLE IF NOT EXISTS `USer_Recommend_List` (
  `listId` int(10) NOT NULL AUTO_INCREMENT,
  `userId` int(10) DEFAULT NULL,
  `listName` varchar(50) DEFAULT NULL,
  `creationDate` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`listId`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=98 ;

--
-- Dumping data for table `USer_Recommend_List`
--

INSERT INTO `USer_Recommend_List` (`listId`, `userId`, `listName`, `creationDate`) VALUES
(3, 2, 'Fair', '2014-12-08 10:21:21'),
(6, 5, 'Recommended', '2014-12-08 17:48:15'),
(8, 17, 'Worst', '2014-12-10 00:15:23'),
(93, 1, 'Luxury', '2015-04-16 19:17:23'),
(94, 21, 'Best', '2015-04-19 14:48:58'),
(95, 1, 'Recommended', '2015-05-19 09:59:22'),
(96, 1, 'Best for dating', '2015-07-14 00:36:02'),
(97, 1, 'Memorable', '2015-07-14 00:37:58');

-- --------------------------------------------------------

--
-- Table structure for table `User`
--

DROP TABLE IF EXISTS `User`;
CREATE TABLE IF NOT EXISTS `User` (
  `userId` int(10) NOT NULL AUTO_INCREMENT,
  `Name` varchar(30) NOT NULL,
  `userName` varchar(30) NOT NULL,
  `password` varchar(30) NOT NULL,
  `emailid` varchar(100) DEFAULT NULL,
  `yearOfBirth` int(4) DEFAULT NULL,
  `cityId` int(10) DEFAULT NULL,
  `countryId` int(10) DEFAULT NULL,
  `trustScore` int(10) DEFAULT '0',
  `lastLogin` datetime DEFAULT '0000-00-00 00:00:00',
  `creationDate` datetime DEFAULT '0000-00-00 00:00:00',
  `updationDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `wall` varchar(400) DEFAULT NULL,
  PRIMARY KEY (`userId`),
  UNIQUE KEY `userName` (`userName`),
  KEY `cityId` (`cityId`),
  KEY `countryId` (`countryId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `User`
--

INSERT INTO `User` (`userId`, `Name`, `userName`, `password`, `emailid`, `yearOfBirth`, `cityId`, `countryId`, `trustScore`, `lastLogin`, `creationDate`, `updationDate`, `wall`) VALUES
(1, 'Tonny', 'user', 'user', 'fff@nn.lk', 1987, 1, 1, 147, '2015-07-17 01:36:37', '2014-12-08 10:21:17', '2014-12-08 10:21:17', 'Welcome! Let''s see what we got!!!'),
(2, 'Rassel Arnold', 'rassel', 'rassel', 'rassel@gmail.com', 1988, 2, 1, 0, '2014-12-08 14:41:11', '2014-12-08 10:21:17', '2014-12-08 10:21:17', 'I feel like going back to college life!'),
(3, 'David Col', 'dav', 'dav', 'dav@gmail.com', 1988, 3, 2, 3, '2015-04-01 04:07:55', '2014-12-08 10:21:17', '2014-12-08 10:21:17', 'Someone help me now! so worried!'),
(4, 'Andrew Symonds', 'andrew', 'andrew', 'andrew@gmail.com', 1988, 4, 2, 0, '2014-12-08 10:21:17', '2014-12-08 10:21:17', '2014-12-08 10:21:17', 'feeling sorry about people sleep on the streets in NYC..'),
(5, 'Tim Cook', 'tim', 'tim', 'tim@gmail.com', 1988, 1, 1, 15, '2014-12-09 19:10:28', '2014-12-08 10:21:17', '2014-12-08 10:21:17', 'Anyting!'),
(6, 'Ziyan Wang', 'ziyan', 'ziyan', 'zy@nyu.edu', 1990, 1, 1, 9, '2014-12-09 19:44:04', '2014-12-08 10:21:17', '2014-12-08 10:21:17', 'Database project is fun! How is everybody doing?'),
(7, 'Sam Wall', 'sam', 'sam', 'sm122@nyu.edu', 1990, 1, 1, 9, '2014-12-08 10:21:17', '2014-12-08 10:21:17', '2014-12-08 10:21:17', 'Nice swimming at NYU gym. Great!!'),
(8, 'Heken Miller', 'hek', 'hek', 'hk22@nyu.edu', 1990, 1, 1, 9, '2014-12-08 10:21:17', '2014-12-08 10:21:17', '2014-12-08 10:21:17', 'Too much course work now! need rest.....'),
(9, 'Chen Tong', 'chen', 'chen', 'chen67@nyu.edu', 1992, 1, 1, 2, '2015-03-16 18:51:06', '2014-12-08 10:21:17', '2014-12-08 10:21:17', 'My girlfriend likes the same music category as I do..!'),
(10, 'Na Li', 'na', 'na', 'cn67@nyu.edu', 1992, 2, 1, 2, '2015-03-21 21:04:32', '2014-12-08 10:21:17', '2014-12-08 10:21:17', 'Just got my first internship at Bloomberg! Cool..'),
(11, 'Raji Rou', 'raj', 'raj', 'rj@siu.edu', 1980, 3, 1, 6, '2014-12-08 10:21:17', '2014-12-08 10:21:17', '2014-12-08 10:21:17', 'Interstellar is too hard to understand.. need to study more.'),
(12, 'Daniel Becker', 'daniel', 'daniel', 'dj@siu.edu', 1980, 4, 1, 6, '2014-12-08 10:21:17', '2014-12-08 10:21:17', '2014-12-08 10:21:17', 'Coding is fun. I should become a good coder in 3 years.'),
(13, 'Totti Fin', 'tot', 'tot', 'totidj@su.edu', 1980, 4, 2, 3, '2014-12-08 10:21:17', '2014-12-08 10:21:17', '2014-12-08 10:21:17', 'My arms are burning after working out.. thanks for coming Dav.'),
(14, 'Leon Fincher', 'leon', 'leon', 'totj@su.edu', 1980, 4, 2, 20, '2014-12-08 10:21:17', '2014-12-08 10:21:17', '2014-12-08 10:21:17', 'Missing target shooting tranings with my former colleagues.. maybe we go shooting out some day uh?'),
(15, 'Danny Archer', 'akk', 'akk', 'akk@nyu.edu', 0, NULL, NULL, 0, '2014-12-08 17:39:49', '2014-12-08 17:29:11', '2014-12-08 17:29:11', ''),
(16, 'Royce', 'rc', 'MEngMEng7', 'rchgogogo2@126.com', 0, NULL, NULL, 7, '2014-12-09 19:45:02', '2014-12-09 19:33:02', '2014-12-09 19:33:02', 'Good day! '),
(17, 'Teng Zhang', 'zhang', 'zhang', 'zt@nyu.edu', 0, 1, NULL, 5, '2014-12-10 00:14:35', '2014-12-10 00:14:18', '2014-12-10 00:14:18', 'I am new user!!!'),
(20, 'Keith', 'tns', 'sss', 'de@nyu.eduss', 0, NULL, NULL, 0, '2015-04-03 16:08:50', '2015-04-03 16:08:50', '2015-04-03 16:08:50', ''),
(21, 'Chuhan', 'chuhan2015', '123456', 'rchgogogo@163.com', 0, 1, NULL, 5, '2015-04-19 14:42:02', '2015-04-19 14:41:42', '2015-04-19 14:41:42', 'Someone is listening..');

-- --------------------------------------------------------

--
-- Table structure for table `User_Artist`
--

DROP TABLE IF EXISTS `User_Artist`;
CREATE TABLE IF NOT EXISTS `User_Artist` (
  `userId` int(10) NOT NULL DEFAULT '0',
  `artistId` int(10) NOT NULL DEFAULT '0',
  `creationDate` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`userId`,`artistId`),
  KEY `artistId` (`artistId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `User_Artist`
--

INSERT INTO `User_Artist` (`userId`, `artistId`, `creationDate`) VALUES
(1, 1, '2015-04-01 01:00:49'),
(1, 3, '2015-04-04 03:50:44'),
(1, 4, '2015-07-14 00:43:20'),
(1, 11, '2015-07-16 23:53:29'),
(2, 3, '2014-12-08 10:21:22'),
(2, 4, '2014-12-08 10:21:22'),
(3, 1, '2014-12-08 10:21:22'),
(5, 1, '2014-12-08 17:34:22');

-- --------------------------------------------------------

--
-- Table structure for table `User_Band`
--

DROP TABLE IF EXISTS `User_Band`;
CREATE TABLE IF NOT EXISTS `User_Band` (
  `userId` int(10) NOT NULL DEFAULT '0',
  `bandId` int(10) NOT NULL DEFAULT '0',
  `creationDate` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`userId`,`bandId`),
  KEY `bandId` (`bandId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `User_Band`
--

INSERT INTO `User_Band` (`userId`, `bandId`, `creationDate`) VALUES
(1, 1, '2015-07-16 23:57:41'),
(1, 2, '2015-04-04 03:50:39'),
(1, 10, '2015-04-04 04:01:12'),
(2, 1, '2014-12-08 10:21:24'),
(2, 2, '2014-12-08 10:21:24'),
(3, 3, '2014-12-08 10:21:24');

-- --------------------------------------------------------

--
-- Table structure for table `User_Bookmark`
--

DROP TABLE IF EXISTS `User_Bookmark`;
CREATE TABLE IF NOT EXISTS `User_Bookmark` (
  `bookmarkId` int(10) NOT NULL AUTO_INCREMENT,
  `userId` int(10) DEFAULT NULL,
  `url` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`bookmarkId`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=132 ;

--
-- Dumping data for table `User_Bookmark`
--

INSERT INTO `User_Bookmark` (`bookmarkId`, `userId`, `url`) VALUES
(124, 1, '/website/php/aprofile.php?artistid=3'),
(125, 1, '/website/php/aprofile.php?artistid=2'),
(126, 1, '/website/php/uprof.php?userid=9'),
(127, 1, '/website/php/uprof.php?userid=11'),
(128, 1, '/website/php/uprof.php?userid=20'),
(129, 1, '/website/php/uprof.php?userid=2'),
(130, 1, '/website/php/bprof.php?bandid=1'),
(131, 1, '/website/php/bprof.php?bandid=10');

-- --------------------------------------------------------

--
-- Table structure for table `User_Followers`
--

DROP TABLE IF EXISTS `User_Followers`;
CREATE TABLE IF NOT EXISTS `User_Followers` (
  `userId` int(10) NOT NULL DEFAULT '0',
  `followingId` int(10) NOT NULL DEFAULT '0',
  `creationDate` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`userId`,`followingId`),
  KEY `followingId` (`followingId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `User_Followers`
--

INSERT INTO `User_Followers` (`userId`, `followingId`, `creationDate`) VALUES
(1, 2, '2015-07-16 23:53:53'),
(1, 3, '2015-04-04 04:00:43'),
(1, 5, '2015-07-14 00:42:49'),
(1, 6, '2015-04-04 03:50:33'),
(1, 9, '2015-07-14 00:42:54'),
(1, 11, '2015-05-19 11:02:37'),
(1, 20, '2015-07-14 00:43:00'),
(2, 1, '2014-12-08 10:21:17'),
(3, 1, '2014-12-08 10:21:18'),
(4, 1, '2014-12-08 10:21:18'),
(4, 2, '2014-12-08 10:21:18'),
(5, 1, '2014-12-08 10:21:18'),
(6, 16, '2014-12-09 19:44:31'),
(10, 1, '2014-12-09 18:37:37'),
(15, 5, '2014-12-08 17:31:50'),
(16, 6, '2014-12-09 19:42:05'),
(17, 6, '2014-12-10 00:16:22'),
(21, 6, '2015-04-19 14:43:35');

-- --------------------------------------------------------

--
-- Table structure for table `User_Music_Category`
--

DROP TABLE IF EXISTS `User_Music_Category`;
CREATE TABLE IF NOT EXISTS `User_Music_Category` (
  `userId` int(10) NOT NULL DEFAULT '0',
  `musicCatId` int(10) NOT NULL DEFAULT '0',
  `creationDate` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`userId`,`musicCatId`),
  KEY `musicCatId` (`musicCatId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `User_Music_Category`
--

INSERT INTO `User_Music_Category` (`userId`, `musicCatId`, `creationDate`) VALUES
(1, 1, '2015-05-19 10:12:04'),
(2, 3, '2014-12-08 10:21:23'),
(3, 1, '2014-12-08 10:21:23'),
(4, 3, '2014-12-08 10:21:23'),
(5, 3, '2014-12-08 10:21:23'),
(6, 3, '2014-12-08 10:21:23'),
(7, 5, '2014-12-08 10:21:23'),
(8, 5, '2014-12-08 10:21:23'),
(9, 3, '2014-12-08 10:21:23'),
(10, 5, '2014-12-08 10:21:23'),
(11, 3, '2014-12-08 10:21:23'),
(12, 3, '2014-12-08 10:21:23'),
(15, 1, '2014-12-08 17:30:35');

-- --------------------------------------------------------

--
-- Stand-in structure for view `artProfile`
--
DROP VIEW IF EXISTS `artProfile`;
CREATE TABLE IF NOT EXISTS `artProfile` (
`artistName` varchar(30)
,`description` varchar(400)
,`websiteLink` varchar(70)
,`wall` varchar(400)
,`artistId` int(10)
,`concertName` varchar(50)
,`mname` varchar(30)
,`subCatName` varchar(30)
,`bandName` varchar(30)
,`bmname` varchar(30)
,`bSubName` varchar(30)
,`countryName` varchar(30)
,`bconName` varchar(50)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `artSearch`
--
DROP VIEW IF EXISTS `artSearch`;
CREATE TABLE IF NOT EXISTS `artSearch` (
`artistName` varchar(30)
,`concertName` varchar(50)
,`concertId` int(10)
,`mname` varchar(30)
,`subCatName` varchar(30)
,`bmname` varchar(30)
,`bSubName` varchar(30)
,`bconName` varchar(50)
,`bconcertId` int(10)
,`cityName` varchar(30)
,`bandName` varchar(30)
,`overallRating` int(10)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `userProfile`
--
DROP VIEW IF EXISTS `userProfile`;
CREATE TABLE IF NOT EXISTS `userProfile` (
`Name` varchar(30)
,`userId` int(10)
,`yearOfBirth` int(4)
,`wall` varchar(400)
,`reviewDescription` varchar(1000)
,`rating` int(10)
,`concertName` varchar(50)
,`mname` varchar(30)
,`artistName` varchar(30)
,`artistId` int(10)
,`bandName` varchar(30)
,`bandId` int(10)
,`fname` varchar(30)
,`followingId` int(10)
,`attname` varchar(50)
,`cityName` varchar(30)
,`countryName` varchar(30)
);
-- --------------------------------------------------------

--
-- Structure for view `artProfile`
--
DROP TABLE IF EXISTS `artProfile`;

CREATE ALGORITHM=UNDEFINED DEFINER=`TODO`@`TODO` SQL SECURITY DEFINER VIEW `artProfile` AS select `a`.`artistName` AS `artistName`,`a`.`description` AS `description`,`a`.`websiteLink` AS `websiteLink`,`a`.`wall` AS `wall`,`a`.`artistId` AS `artistId`,`ct`.`concertName` AS `concertName`,`mc`.`name` AS `mname`,`ms`.`subCatName` AS `subCatName`,`b`.`bandname` AS `bandName`,`mc1`.`name` AS `bmname`,`ms1`.`subCatName` AS `bSubName`,`cou`.`countryName` AS `countryName`,`ct1`.`concertName` AS `bconName` from (((((((((((((`Artist` `a` left join `Artist_Concert` `ac` on((`ac`.`artistId` = `a`.`artistId`))) left join `Concert` `ct` on((`ac`.`concertId` = `ct`.`concertId`))) left join `ARTIST_Music_Category` `amc` on((`amc`.`artistId` = `a`.`artistId`))) left join `Music_Category` `mc` on((`amc`.`musicCatId` = `mc`.`musicCatId`))) left join `ARTIST_Music_SubCategory` `ams` on((`a`.`artistId` = `ams`.`artistId`))) left join `Music_SubCategory` `ms` on((`ms`.`subCatId` = `ams`.`subCatId`))) left join `Band` `b` on((`b`.`bandId` = `a`.`bandId`))) left join `Band_M_Cat` `bmc` on((`bmc`.`bandId` = `b`.`bandId`))) left join `Music_Category` `mc1` on((`mc1`.`musicCatId` = `bmc`.`musicCatId`))) left join `Band_M_Sub` `bms` on((`bms`.`bandId` = `b`.`bandId`))) left join `Music_SubCategory` `ms1` on((`ms1`.`subCatId` = `bms`.`subCatId`))) left join `Country` `cou` on((`cou`.`countryId` = `a`.`countryId`))) left join `Concert` `ct1` on((`ct1`.`bandId` = `b`.`bandId`)));

-- --------------------------------------------------------

--
-- Structure for view `artSearch`
--
DROP TABLE IF EXISTS `artSearch`;

CREATE ALGORITHM=UNDEFINED DEFINER=`TODO`@`TODO` SQL SECURITY DEFINER VIEW `artSearch` AS select `a`.`artistName` AS `artistName`,`ct`.`concertName` AS `concertName`,`ct`.`concertId` AS `concertId`,`mc`.`name` AS `mname`,`ms`.`subCatName` AS `subCatName`,`mc1`.`name` AS `bmname`,`ms1`.`subCatName` AS `bSubName`,`ct1`.`concertName` AS `bconName`,`ct1`.`concertId` AS `bconcertId`,`c`.`cityName` AS `cityName`,`b`.`bandname` AS `bandName`,`ct`.`overallRating` AS `overallRating` from ((((((((((((((`Artist` `a` left join `Artist_Concert` `ac` on((`ac`.`artistId` = `a`.`artistId`))) left join `Concert` `ct` on((`ac`.`concertId` = `ct`.`concertId`))) left join `ARTIST_Music_Category` `amc` on((`amc`.`artistId` = `a`.`artistId`))) left join `Music_Category` `mc` on((`amc`.`musicCatId` = `mc`.`musicCatId`))) left join `ARTIST_Music_SubCategory` `ams` on((`a`.`artistId` = `ams`.`artistId`))) left join `Music_SubCategory` `ms` on((`ms`.`subCatId` = `ams`.`subCatId`))) left join `Band` `b` on((`b`.`bandId` = `a`.`bandId`))) left join `Band_M_Cat` `bmc` on((`bmc`.`bandId` = `b`.`bandId`))) left join `Music_Category` `mc1` on((`mc1`.`musicCatId` = `bmc`.`musicCatId`))) left join `Band_M_Sub` `bms` on((`bms`.`bandId` = `b`.`bandId`))) left join `Music_SubCategory` `ms1` on((`ms1`.`subCatId` = `bms`.`subCatId`))) left join `Location` `loc` on((`loc`.`locationId` = `ct`.`locationId`))) left join `City` `c` on((`c`.`cityId` = `loc`.`cityId`))) left join `Concert` `ct1` on((`ct1`.`bandId` = `b`.`bandId`)));

-- --------------------------------------------------------

--
-- Structure for view `userProfile`
--
DROP TABLE IF EXISTS `userProfile`;

CREATE ALGORITHM=UNDEFINED DEFINER=`TODO`@`TODO` SQL SECURITY DEFINER VIEW `userProfile` AS select `u`.`Name` AS `Name`,`u`.`userId` AS `userId`,`u`.`yearOfBirth` AS `yearOfBirth`,`u`.`wall` AS `wall`,`crr`.`reviewDescription` AS `reviewDescription`,`crr`.`rating` AS `rating`,`ct`.`concertName` AS `concertName`,`mc`.`name` AS `mname`,`ar`.`artistName` AS `artistName`,`ar`.`artistId` AS `artistId`,`Band`.`bandname` AS `bandName`,`Band`.`bandId` AS `bandId`,`p`.`Name` AS `fname`,`uf`.`followingId` AS `followingId`,`con`.`concertName` AS `attname`,`City`.`cityName` AS `cityName`,`Country`.`countryName` AS `countryName` from ((((((((((((((`User` `u` left join `Concert_Review_Rating` `crr` on((`u`.`userId` = `crr`.`userId`))) left join `Concert` `ct` on((`ct`.`concertId` = `crr`.`concertId`))) left join `User_Music_Category` `umc` on((`u`.`userId` = `umc`.`userId`))) left join `Music_Category` `mc` on((`umc`.`musicCatId` = `mc`.`musicCatId`))) left join `User_Artist` `ua` on((`u`.`userId` = `ua`.`userId`))) left join `Artist` `ar` on((`ua`.`artistId` = `ar`.`artistId`))) left join `User_Band` `ub` on((`u`.`userId` = `ub`.`userId`))) left join `Band` on((`ub`.`bandId` = `Band`.`bandId`))) left join `User_Followers` `uf` on((`u`.`userId` = `uf`.`userId`))) left join `User` `p` on((`uf`.`followingId` = `p`.`userId`))) left join `Concert_Attend_User` `cau` on((`u`.`userId` = `cau`.`userId`))) left join `Concert` `con` on((`cau`.`concertId` = `con`.`concertId`))) left join `City` on((`u`.`cityId` = `City`.`cityId`))) left join `Country` on((`u`.`countryId` = `Country`.`countryId`)));

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ARTIST_Music_Category`
--
ALTER TABLE `ARTIST_Music_Category`
  ADD CONSTRAINT `ARTIST_Music_Category_ibfk_1` FOREIGN KEY (`artistId`) REFERENCES `Artist` (`artistId`),
  ADD CONSTRAINT `ARTIST_Music_Category_ibfk_2` FOREIGN KEY (`musicCatId`) REFERENCES `Music_Category` (`musicCatId`);

--
-- Constraints for table `ARTIST_Music_SubCategory`
--
ALTER TABLE `ARTIST_Music_SubCategory`
  ADD CONSTRAINT `ARTIST_Music_SubCategory_ibfk_1` FOREIGN KEY (`artistId`) REFERENCES `Artist` (`artistId`),
  ADD CONSTRAINT `ARTIST_Music_SubCategory_ibfk_2` FOREIGN KEY (`subCatId`) REFERENCES `Music_SubCategory` (`subCatId`);

--
-- Constraints for table `Artist`
--
ALTER TABLE `Artist`
  ADD CONSTRAINT `Artist_ibfk_1` FOREIGN KEY (`bandId`) REFERENCES `Band` (`bandId`),
  ADD CONSTRAINT `Artist_ibfk_2` FOREIGN KEY (`countryId`) REFERENCES `Country` (`countryId`);

--
-- Constraints for table `Artist_Concert`
--
ALTER TABLE `Artist_Concert`
  ADD CONSTRAINT `Artist_Concert_ibfk_1` FOREIGN KEY (`artistId`) REFERENCES `Artist` (`artistId`),
  ADD CONSTRAINT `Artist_Concert_ibfk_2` FOREIGN KEY (`concertId`) REFERENCES `Concert` (`concertId`);

--
-- Constraints for table `Band_M_Cat`
--
ALTER TABLE `Band_M_Cat`
  ADD CONSTRAINT `Band_M_Cat_ibfk_1` FOREIGN KEY (`bandId`) REFERENCES `Band` (`bandId`),
  ADD CONSTRAINT `Band_M_Cat_ibfk_2` FOREIGN KEY (`musicCatId`) REFERENCES `Music_Category` (`musicCatId`);

--
-- Constraints for table `Band_M_Sub`
--
ALTER TABLE `Band_M_Sub`
  ADD CONSTRAINT `Band_M_Sub_ibfk_1` FOREIGN KEY (`bandId`) REFERENCES `Band` (`bandId`),
  ADD CONSTRAINT `Band_M_Sub_ibfk_2` FOREIGN KEY (`subCatId`) REFERENCES `Music_SubCategory` (`subCatId`);

--
-- Constraints for table `City`
--
ALTER TABLE `City`
  ADD CONSTRAINT `City_ibfk_1` FOREIGN KEY (`countryId`) REFERENCES `Country` (`countryId`);

--
-- Constraints for table `Concert`
--
ALTER TABLE `Concert`
  ADD CONSTRAINT `Concert_ibfk_1` FOREIGN KEY (`bandId`) REFERENCES `Band` (`bandId`),
  ADD CONSTRAINT `Concert_ibfk_2` FOREIGN KEY (`locationId`) REFERENCES `Location` (`locationId`);

--
-- Constraints for table `Concert_Attend_User`
--
ALTER TABLE `Concert_Attend_User`
  ADD CONSTRAINT `Concert_Attend_User_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `User` (`userId`),
  ADD CONSTRAINT `Concert_Attend_User_ibfk_2` FOREIGN KEY (`concertId`) REFERENCES `Concert` (`concertId`);

--
-- Constraints for table `Concert_Review_Rating`
--
ALTER TABLE `Concert_Review_Rating`
  ADD CONSTRAINT `Concert_Review_Rating_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `User` (`userId`),
  ADD CONSTRAINT `Concert_Review_Rating_ibfk_2` FOREIGN KEY (`concertId`) REFERENCES `Concert` (`concertId`);

--
-- Constraints for table `Location`
--
ALTER TABLE `Location`
  ADD CONSTRAINT `Location_ibfk_1` FOREIGN KEY (`cityId`) REFERENCES `City` (`cityId`);

--
-- Constraints for table `Music_SubCategory`
--
ALTER TABLE `Music_SubCategory`
  ADD CONSTRAINT `Music_SubCategory_ibfk_1` FOREIGN KEY (`musicCatId`) REFERENCES `Music_Category` (`musicCatId`);

--
-- Constraints for table `Recommend_List_Concert`
--
ALTER TABLE `Recommend_List_Concert`
  ADD CONSTRAINT `Recommend_List_Concert_ibfk_1` FOREIGN KEY (`listId`) REFERENCES `USer_Recommend_List` (`listId`),
  ADD CONSTRAINT `Recommend_List_Concert_ibfk_2` FOREIGN KEY (`concertId`) REFERENCES `Concert` (`concertId`);

--
-- Constraints for table `USer_Recommend_List`
--
ALTER TABLE `USer_Recommend_List`
  ADD CONSTRAINT `USer_Recommend_List_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `User` (`userId`);

--
-- Constraints for table `User`
--
ALTER TABLE `User`
  ADD CONSTRAINT `User_ibfk_1` FOREIGN KEY (`cityId`) REFERENCES `City` (`cityId`),
  ADD CONSTRAINT `User_ibfk_2` FOREIGN KEY (`countryId`) REFERENCES `Country` (`countryId`);

--
-- Constraints for table `User_Artist`
--
ALTER TABLE `User_Artist`
  ADD CONSTRAINT `User_Artist_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `User` (`userId`),
  ADD CONSTRAINT `User_Artist_ibfk_2` FOREIGN KEY (`artistId`) REFERENCES `Artist` (`artistId`);

--
-- Constraints for table `User_Band`
--
ALTER TABLE `User_Band`
  ADD CONSTRAINT `User_Band_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `User` (`userId`),
  ADD CONSTRAINT `User_Band_ibfk_2` FOREIGN KEY (`bandId`) REFERENCES `Band` (`bandId`);

--
-- Constraints for table `User_Bookmark`
--
ALTER TABLE `User_Bookmark`
  ADD CONSTRAINT `User_Bookmark_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `User` (`userId`);

--
-- Constraints for table `User_Followers`
--
ALTER TABLE `User_Followers`
  ADD CONSTRAINT `User_Followers_ibfk_1` FOREIGN KEY (`followingId`) REFERENCES `User` (`userId`);

--
-- Constraints for table `User_Music_Category`
--
ALTER TABLE `User_Music_Category`
  ADD CONSTRAINT `User_Music_Category_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `User` (`userId`),
  ADD CONSTRAINT `User_Music_Category_ibfk_2` FOREIGN KEY (`musicCatId`) REFERENCES `Music_Category` (`musicCatId`);


