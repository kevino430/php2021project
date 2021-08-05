-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- 主機: 127.0.0.1
-- 產生時間： 
-- 伺服器版本: 10.1.16-MariaDB
-- PHP 版本： 7.0.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `project_2021_final`
--

-- --------------------------------------------------------

--
-- 資料表結構 `category`
--

CREATE TABLE `category` (
  `categoryid` int(11) UNSIGNED NOT NULL,
  `categoryname` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `categorysort` int(11) UNSIGNED DEFAULT NULL,
  `categoryimg` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `category_valid` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 資料表的匯出資料 `category`
--

INSERT INTO `category` (`categoryid`, `categoryname`, `categorysort`, `categoryimg`, `category_valid`) VALUES
(1, ' ASUS', 1, 'asus_logo.png', 1),
(2, 'ACER', 2, 'acer_logo.png', 1),
(3, 'HP', 3, 'hp_logo.png', 1),
(4, 'SONY', 4, 'sony_logo.png', 1),
(5, 'DELL', 5, 'dell-logo.png', 1),
(39, 'MSI', 0, 'msi_logo.png', 1),
(44, 'APPLE', 0, 'Apple_logo.png', 1);

-- --------------------------------------------------------

--
-- 資料表結構 `memberdata`
--

CREATE TABLE `memberdata` (
  `m_id` int(11) NOT NULL,
  `m_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `m_username` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `m_passwd` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `m_sex` enum('男','女') COLLATE utf8_unicode_ci NOT NULL,
  `m_birthday` date DEFAULT NULL,
  `m_level` enum('admin','member') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'member',
  `m_email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `m_url` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `m_phone` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `m_address` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `m_login` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `m_logintime` datetime DEFAULT NULL,
  `m_jointime` datetime NOT NULL,
  `m_valid` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 資料表的匯出資料 `memberdata`
--

INSERT INTO `memberdata` (`m_id`, `m_name`, `m_username`, `m_passwd`, `m_sex`, `m_birthday`, `m_level`, `m_email`, `m_url`, `m_phone`, `m_address`, `m_login`, `m_logintime`, `m_jointime`, `m_valid`) VALUES
(1, '系統管理員', 'admin', '$2y$10$FO70lc.3/vTeE0Vaf7O3Jes.UArylzLnnxfZffTF7410vndnvhScm', '男', NULL, 'admin', NULL, NULL, NULL, NULL, 263, '2021-08-05 08:21:46', '2008-10-20 16:36:15', 1),
(2, '張惠玲', 'elven', '$2y$10$YdUhOvUTvwK5oWp/i3LafOd2ImwsE/85YmmoY2konsxdmMSsvczFO', '女', '1987-04-05', 'member', 'elven@superstar.com', '', '0966765556', '台北市濟洲北路12號2樓', 12, '2016-08-29 11:44:33', '2008-10-21 12:03:12', 0),
(3, '彭建志', 'jinglun', '$2y$10$WqB2bnMSO/wgBiHSOBV2iuLbrUCsp8VmNJdK2AyIW6IANUL9jeFjC', '男', '1987-07-01', 'member', 'jinglun@superstar.com', '', '0918181111', '台北市敦化南路93號5樓', 0, NULL, '2008-10-21 12:06:08', 1),
(4, '謝耿鴻', 'sugie', '$2y$10$6uWtdYATI3b/wMRk.AaqIei852PLf.WjeKm8X.Asl0VTmpxCkqbW6', '男', '1987-08-11', 'member', 'edreamer@gmail.com', '', '0914530768', '台北市中央路201號7樓', 2, '2016-08-29 14:03:53', '2008-10-21 12:06:08', 1),
(5, '蔣志明', 'shane', '$2y$10$pWefN9xkeXOKCx59GF6ZJuSGNnIFBY4q/DCmCvAwOFtnoTCujb3Te', '男', '1984-06-20', 'member', 'shane@superstar.com', NULL, '0946820035', '台北市建國路177號6樓', 0, NULL, '2008-10-21 12:06:08', 1),
(6, '王佩珊', 'ivy', '$2y$10$RPrt3YfaSs0d82inYIK6he.JaPqOrisWMqASuxN5g62EyRio.lyEa', '女', '1988-02-15', 'member', 'ivy@superstar.com', NULL, '0920981230', '台北市忠孝東路520號6樓', 0, NULL, '2008-10-21 12:06:08', 1),
(7, '林志宇', 'zhong', '$2y$10$pee.jvO6f4sSKahlc4cLLO9RUMyx8aphyqkSUdwHTNSy4Ax7YPdpq', '男', '1987-05-05', 'member', 'zhong@superstar.com', NULL, '0951983366', '台北市三民路1巷10號', 0, NULL, '2008-10-21 12:06:08', 1),
(8, '李曉薇', 'lala', '$2y$10$oiC9CaGiOdWu.6w5b3.b/Ora6fSuh8Lrbj8Kg5BUPT15O3QptksQS', '女', '1985-08-30', 'member', 'lala@superstar.com', NULL, '0918123456', '台北市仁愛路100號', 0, NULL, '2008-10-21 12:06:08', 1),
(9, '賴秀英', 'crystal', '$2y$10$8Q0.JEGILRM91qAlMmWnB.wpcY.rJEbgNgV5ntIlqZmdGaHPwikji', '女', '1986-12-10', 'member', 'crystal@superstar.com', NULL, '0907408965', '台北市民族路204號', 0, NULL, '2008-10-21 12:06:08', 1),
(10, '張雅琪', 'peggy', '$2y$10$RNqnXDNHkcTI2Zh2bkTKnOesz0FLXhihhT8ZL8OHoMeYSq7jsILMi', '女', '1988-12-01', 'member', 'peggy@superstar.com', NULL, '0916456723', '台北市建國北路10號', 0, NULL, '2008-10-21 12:06:08', 1),
(11, '陳燕博', 'albert', '$2y$10$seMLwqcQRQiWa0jMBAcMMertjLbrPLRGNZoKc0NZ5FxTwWha7W3lm', '男', '1993-08-10', 'member', 'albert@superstar.com', NULL, '0918976588', '台北市北環路2巷80號', 0, NULL, '2008-10-21 12:06:08', 1),
(12, '黃信溢', 'dkdreamer', '$2y$10$Fx0rLJtV5mVtJzAJ52B/hup1AmviTe7Ciu0mtWBCZAkYC0qmg6OJy', '女', '1987-04-05', 'member', 'edreamer@gmail.com', '', '955648889', '愛蘭里梅村路213巷8號', 1, '2016-08-29 17:42:24', '2016-08-29 17:41:46', 1),
(13, 'Kevin Tuan', 'kevino430', '$2y$10$NDTeGMvT.4MGhOV3KhAgz.w58lYJltBxYetrsM.Il9iuGMrdoLFbe', '男', '2021-05-01', 'member', 'zongyongduan23@gmail.com', '', '0970619427', '台北市敦化南路93號5樓', 50, '2021-07-31 09:02:56', '2021-05-10 00:33:54', 1),
(14, 'pooh3838', 'pooh3838', '$2y$10$M/sVgmlf2jsgCm/RHqrfVuh0TyqiOk7fG/D27subPdNCePQzMLynu', '男', '2021-05-06', 'member', 'test1234@testmail.com', '', '0970619427', '台北市敦化南路93號5樓', 125, '2021-08-04 22:06:51', '2021-05-10 00:35:39', 1),
(15, 'pooh3838', 'k0001', '$2y$10$uDumsY2HryYZp1RneW.kZuOJAt8fPQ2aHp0j31AUf7cKkkwZ3RyeW', '男', '2021-05-05', 'member', 'testemail@gmail.com', '', '0970619427', '', 7, '2021-06-02 18:30:45', '2021-05-10 10:14:27', 1),
(16, 'kuoto', 'kuoto80259', '$2y$10$FupNpjK3mNKPt2nEg4oeueIRdXVq9rz/.3mMbrCs6WQIt3yOh88BO', '男', '1963-03-31', 'member', 'email@testemail.com', '', '123456789', '桃園市中壢區中大路300 號', 1, '2021-05-12 22:43:53', '2021-05-12 22:43:09', 1),
(17, 'kevintuan', 'kevin001', '$2y$10$DdQeqOYhVIckpBaWcwNatuFZKLhu7LcUPoj0fRIZim5e2e14fPOqG', '男', '2021-05-19', 'member', 'testemail@testmail.com', '', '0970619427', '台北市敦化南路93號5樓', 3, '2021-07-06 17:12:27', '2021-05-20 14:13:04', 1),
(18, 'jane58', 'jane58', '$2y$10$PncWQw2qNUK.1Y0e7hGY8eSb22.obtCYEhB42X8hypNrJ6CzT.0P2', '女', '2021-05-27', 'member', 'testemail@gmail.com', '', '0970619427', '台北市敦化南路93號5樓', 100, '2021-07-20 22:45:24', '2021-05-27 23:46:48', 1);

-- --------------------------------------------------------

--
-- 資料表結構 `orderdetail`
--

CREATE TABLE `orderdetail` (
  `orderdetailid` int(11) UNSIGNED NOT NULL,
  `orderid` int(11) UNSIGNED DEFAULT NULL,
  `productid` int(11) UNSIGNED DEFAULT NULL,
  `productname` varchar(254) COLLATE utf8_unicode_ci DEFAULT NULL,
  `unitprice` int(11) UNSIGNED DEFAULT NULL,
  `quantity` int(11) UNSIGNED DEFAULT NULL,
  `od_valid` int(3) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 資料表的匯出資料 `orderdetail`
--

INSERT INTO `orderdetail` (`orderdetailid`, `orderid`, `productid`, `productname`, `unitprice`, `quantity`, `od_valid`) VALUES
(1, 1, 20, 'kevin002', 1200, 1, 1),
(2, 1, 19, 'kevin product 001', 54321, 1, 1),
(3, 2, 20, 'kevin002', 1200, 1, 1),
(4, 2, 19, 'kevin product 001', 54321, 1, 1),
(5, 2, 18, 'SONY VGN-FE25TP', 49800, 1, 1),
(6, 3, 20, 'kevin002', 1200, 4, 1),
(7, 4, 19, 'kevin product 001', 54321, 3, 1),
(8, 4, 13, 'HP V2632', 39900, 1, 1),
(9, 5, 20, 'kevin002', 1200, 3, 1),
(10, 6, 34, 'a001', 123, 11, 1),
(11, 7, 34, 'a001', 123, 1, 1),
(12, 7, 33, 'KEVIN PRODUCT A001', 10000, 1, 1),
(13, 8, 37, 'a004', 12345, 10, 1),
(14, 9, 38, 'KEVIN PRODUCT A101', 20000, 20, 1),
(15, 10, 38, 'KEVIN PRODUCT A101', 20000, 1, 1),
(16, 11, 19, 'kevin product 001', 54321, 1, 1),
(17, 11, 17, 'SONY VAIO VGN-FJ79TP', 39800, 8, 1),
(18, 12, 19, 'kevin product 001', 54321, 1, 1),
(19, 12, 16, 'SONY VGN-AR18TP', 149800, 1, 1),
(20, 13, 19, 'kevin product 001', 54321, 1, 1),
(21, 13, 16, 'SONY VGN-AR18TP', 149800, 1, 1),
(22, 14, 19, 'kevin product 001', 54321, 1, 1),
(23, 14, 16, 'SONY VGN-AR18TP', 149800, 1, 1),
(24, 15, 19, 'kevin product 001', 54321, 1, 1),
(25, 15, 16, 'SONY VGN-AR18TP', 149800, 1, 1),
(26, 16, 19, 'kevin product 001', 54321, 1, 1),
(27, 16, 16, 'SONY VGN-AR18TP', 149800, 1, 1),
(28, 17, 2, 'ASUS F3APT24DD', 51800, 1, 1),
(29, 17, 5, 'ASUS VX1', 108800, 2, 1),
(30, 18, 16, 'SONY VGN-AR18TP', 149800, 1, 1),
(31, 18, 19, 'kevin product 001', 54321, 1, 1),
(32, 19, 17, 'SONY VAIO VGN-FJ79TP', 39800, 1, 1),
(33, 20, 20, 'kevin002', 1200, 1, 1),
(34, 20, 19, 'kevin product 001', 54321, 1, 1),
(35, 21, 20, 'kevin002', 1200, 1, 1),
(36, 21, 19, 'kevin product 001', 54321, 1, 1),
(37, 22, 4, 'ASUS S6F', 68800, 1, 1),
(38, 22, 5, 'ASUS VX1', 108800, 1, 1),
(39, 23, 19, 'kevin product 001', 54321, 1, 1),
(40, 23, 20, 'kevin002', 1200, 1, 1),
(41, 24, 19, 'kevin product 001', 54321, 1, 1),
(42, 24, 18, 'SONY VGN-FE25TP', 49800, 1, 1),
(43, 24, 17, 'SONY VAIO VGN-FJ79TP', 39800, 1, 1),
(44, 25, 16, 'SONY VGN-AR18TP', 149800, 1, 1),
(45, 25, 15, 'HP Pavilion dv4213AP', 39900, 1, 1),
(46, 25, 14, 'HP Presario B2821', 36900, 1, 1),
(47, 25, 13, 'HP V2632', 39900, 1, 1),
(48, 26, 18, 'SONY VGN-FE25TP', 49800, 1, 1),
(49, 26, 17, 'SONY VAIO VGN-FJ79TP', 39800, 1, 1),
(50, 26, 13, 'HP V2632', 39900, 1, 1),
(51, 27, 4, 'ASUS S6F', 68800, 1, 1),
(52, 27, 3, 'ASUS W7J', 58800, 1, 1),
(53, 27, 2, 'ASUS F3APT24DD', 51800, 1, 1),
(54, 28, 16, 'SONY VGN-AR18TP', 149800, 1, 1),
(55, 28, 17, 'SONY VAIO VGN-FJ79TP', 39800, 1, 1),
(56, 29, 5, 'ASUS VX1', 108800, 1, 1),
(57, 29, 4, 'ASUS S6F', 68800, 1, 1),
(58, 29, 3, 'ASUS W7J', 58800, 1, 1),
(59, 30, 19, 'kevin product 001', 54321, 1, 1),
(60, 30, 17, 'SONY VAIO VGN-FJ79TP', 39800, 1, 1),
(61, 31, 10, 'ACER 3022WTMi', 52900, 1, 1),
(62, 31, 9, 'ACER Ferrari 4002WLMi', 48800, 1, 1),
(63, 32, 4, 'ASUS S6F', 68800, 1, 1),
(64, 32, 3, 'ASUS W7J', 58800, 1, 1),
(65, 33, 19, 'kevin product 001', 54321, 1, 1),
(66, 33, 20, 'kevin002', 1200, 1, 1),
(67, 34, 16, 'SONY VGN-AR18TP', 149800, 1, 1),
(68, 34, 15, 'HP Pavilion dv4213AP', 39900, 1, 1),
(69, 34, 14, 'HP Presario B2821', 36900, 1, 1),
(70, 35, 19, 'kevin product 001', 54321, 1, 1),
(71, 35, 18, 'SONY VGN-FE25TP', 49800, 1, 1),
(72, 37, 4, 'ASUS S6F', 68800, 1, 1),
(73, 37, 3, 'ASUS W7J', 58800, 1, 1),
(74, 38, 20, 'kevin002', 1200, 1, 1),
(75, 38, 19, 'kevin product 001', 54321, 1, 1),
(76, 38, 18, 'SONY VGN-FE25TP', 49800, 1, 1),
(77, 38, 17, 'SONY VAIO VGN-FJ79TP', 39800, 1, 1),
(78, 39, 19, 'kevin product 001', 54321, 1, 1),
(79, 39, 18, 'SONY VGN-FE25TP', 49800, 1, 1),
(80, 40, 18, 'SONY VGN-FE25TP', 49800, 1, 1),
(81, 40, 17, 'SONY VAIO VGN-FJ79TP', 39800, 1, 1),
(82, 41, 19, 'kevin product 001', 54321, 1, 1),
(83, 41, 18, 'SONY VGN-FE25TP', 49800, 1, 1),
(84, 42, 4, 'ASUS S6F', 68800, 1, 1),
(85, 42, 3, 'ASUS W7J', 58800, 1, 1),
(86, 43, 9, 'ACER Ferrari 4002WLMi', 48800, 1, 1),
(87, 43, 8, 'ACER 5562', 48900, 1, 1),
(88, 44, 14, 'HP Presario B2821', 36900, 1, 1),
(89, 44, 13, 'HP V2632', 39900, 1, 1),
(90, 45, 4, 'ASUS S6F', 68800, 1, 1),
(91, 45, 3, 'ASUS W7J', 58800, 1, 1),
(92, 46, 19, 'kevin product 001', 54321, 1, 1),
(93, 46, 18, 'SONY VGN-FE25TP', 49800, 1, 1),
(94, 47, 4, 'ASUS S6F', 68800, 1, 1),
(95, 47, 3, 'ASUS W7J', 58800, 1, 1),
(96, 49, 4, 'ASUS S6F', 68800, 1, 1),
(97, 49, 3, 'ASUS W7J', 58800, 1, 1),
(98, 50, 10, 'ACER 3022WTMi', 52900, 1, 1),
(99, 50, 9, 'ACER Ferrari 4002WLMi', 48800, 1, 1),
(100, 51, 13, 'HP V2632', 39900, 1, 1),
(101, 51, 12, 'HP NC2400', 55900, 1, 1),
(102, 52, 13, 'HP V2632', 39900, 1, 1),
(103, 52, 12, 'HP NC2400', 55900, 1, 1),
(104, 53, 4, 'ASUS S6F', 68800, 1, 1),
(105, 53, 3, 'ASUS W7J', 58800, 1, 1),
(106, 54, 20, 'kevin002', 1200, 1, 1),
(107, 54, 19, 'kevin product 001', 54321, 1, 1),
(108, 55, 20, 'kevin002', 1200, 90, 1),
(109, 56, 19, 'kevin product 001', 54321, 1, 1),
(110, 56, 18, 'SONY VGN-FE25TP', 49800, 1, 1),
(111, 57, 20, 'kevin002', 1200, 3, 1),
(112, 57, 19, 'kevin product 001', 54321, 3, 1),
(113, 58, 18, 'SONY VGN-FE25TP', 49800, 1, 1),
(114, 58, 19, 'kevin product 001', 54321, 2, 1),
(115, 58, 20, 'kevin002', 1200, 1, 1),
(116, 59, 20, 'kevin002', 1200, 1, 0),
(117, 59, 19, 'kevin product 001', 54321, 1, 0),
(118, 60, 19, 'kevin product 001', 54321, 1, 0),
(119, 60, 20, 'kevin002', 1200, 1, 0),
(120, 61, 16, 'SONY VGN-AR18TP', 149800, 1, 0),
(121, 61, 15, 'HP Pavilion dv4213AP', 39900, 1, 0),
(122, 62, 17, 'SONY VAIO VGN-FJ79TP', 39800, 1, 1);

-- --------------------------------------------------------

--
-- 資料表結構 `orders`
--

CREATE TABLE `orders` (
  `orderid` int(11) UNSIGNED NOT NULL,
  `invoice` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `member_id` int(10) UNSIGNED NOT NULL,
  `total` int(11) UNSIGNED DEFAULT NULL,
  `deliverfee` int(11) UNSIGNED DEFAULT NULL,
  `grandtotal` int(11) UNSIGNED DEFAULT NULL,
  `customername` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `customeremail` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `customeraddress` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `customerphone` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `paytype` enum('ATM匯款','線上刷卡','貨到付款') COLLATE utf8_unicode_ci DEFAULT 'ATM匯款',
  `ordertime` datetime NOT NULL,
  `or_valid` int(3) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 資料表的匯出資料 `orders`
--

INSERT INTO `orders` (`orderid`, `invoice`, `member_id`, `total`, `deliverfee`, `grandtotal`, `customername`, `customeremail`, `customeraddress`, `customerphone`, `paytype`, `ordertime`, `or_valid`) VALUES
(1, 'IN2021070001', 0, 55521, 0, 55521, '1223', '1234@1253.com', '215165', '12365', 'ATM匯款', '2021-07-12 11:39:56', 1),
(2, 'IN2021070002', 14, 105321, 0, 105321, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', '線上刷卡', '2021-07-12 11:42:40', 1),
(3, 'IN2021070003', 0, 4800, 500, 5300, '1223', '1234@1253.com', '215165', '12365', 'ATM匯款', '2021-07-12 11:47:44', 1),
(4, 'IN2021070004', 0, 202863, 0, 202863, '111', '1111@1253.com', '1111111', '1111', '線上刷卡', '2021-07-14 18:39:57', 1),
(5, 'IN2021070005', 0, 3600, 500, 4100, '1223', '1234@1253.com', '215165', '12365', 'ATM匯款', '2021-07-14 20:45:38', 1),
(6, 'IN2021070006', 0, 1353, 500, 1853, '1223', '1234@1253.com', '215165', '12365', 'ATM匯款', '2021-07-17 18:03:55', 1),
(7, 'IN2021070007', 14, 10123, 500, 10623, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-17 20:09:34', 1),
(8, 'IN2021070008', 14, 123450, 0, 123450, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-17 20:11:22', 1),
(9, 'IN2021070009', 0, 400000, 0, 400000, 'kevino430', '1234@1253.com', '215165', '12365', '線上刷卡', '2021-07-17 20:14:16', 1),
(10, 'IN2021070010', 14, 20000, 500, 20500, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-17 20:15:31', 1),
(11, 'IN2021070011', 14, 372721, 0, 372721, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-19 14:25:39', 1),
(12, 'IN2021070012', 14, 204121, 0, 204121, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-19 14:28:41', 1),
(13, 'IN2021070013', 14, 204121, 0, 204121, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-19 14:29:50', 1),
(14, 'IN2021070014', 0, 204121, 0, 204121, '1223', '1234@1253.com', '215165', '12365', 'ATM匯款', '2021-07-20 13:54:13', 1),
(15, 'IN2021070015', 0, 204121, 0, 204121, '1223', '1234@1253.com', '215165', '12365', 'ATM匯款', '2021-07-20 22:35:35', 1),
(16, 'IN2021070016', 0, 204121, 0, 204121, '1223', '1234@1253.com', '215165', '12365', 'ATM匯款', '2021-07-20 22:43:12', 1),
(17, 'IN2021070017', 18, 269400, 0, 269400, 'jane58', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-20 22:46:17', 1),
(18, 'IN2021070018', 14, 204121, 0, 204121, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 13:50:03', 1),
(19, 'IN2021070019', 14, 39800, 500, 40300, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 14:40:12', 1),
(20, 'IN2021070020', 14, 55521, 0, 55521, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 14:53:51', 1),
(21, 'IN2021070021', 14, 55521, 0, 55521, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 16:02:11', 1),
(22, 'IN2021070022', 14, 177600, 0, 177600, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', '貨到付款', '2021-07-21 16:08:18', 1),
(23, 'IN2021070023', 14, 55521, 0, 55521, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 16:13:06', 1),
(24, 'IN2021070024', 14, 143921, 0, 143921, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 16:13:35', 1),
(25, 'IN2021070025', 14, 266500, 0, 266500, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 16:22:25', 1),
(26, 'IN2021070026', 14, 129500, 0, 129500, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 16:30:13', 1),
(27, 'IN2021070027', 14, 179400, 0, 179400, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 16:32:19', 1),
(28, 'IN2021070028', 14, 189600, 0, 189600, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 16:33:52', 1),
(29, 'IN2021070029', 14, 236400, 0, 236400, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 16:44:29', 1),
(30, 'IN2021070030', 14, 94121, 0, 94121, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 16:49:17', 1),
(31, 'IN2021070031', 14, 101700, 0, 101700, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 16:54:11', 1),
(32, 'IN2021070032', 14, 127600, 0, 127600, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 16:55:22', 1),
(33, 'IN2021070033', 14, 55521, 0, 55521, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 17:38:31', 1),
(34, 'IN2021070034', 14, 226600, 0, 226600, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 17:47:58', 1),
(35, 'IN2021070035', 14, 104121, 0, 104121, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 17:55:50', 1),
(36, 'IN2021070036', 14, 0, 0, 0, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 17:56:23', 0),
(37, 'IN2021070037', 14, 127600, 0, 127600, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 17:56:37', 1),
(38, 'IN2021070038', 14, 145121, 0, 145121, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 18:06:29', 1),
(39, 'IN2021070039', 14, 104121, 0, 104121, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 18:08:04', 1),
(40, 'IN2021070040', 14, 89600, 0, 89600, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 18:13:40', 1),
(41, 'IN2021070041', 14, 104121, 0, 104121, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 18:14:44', 1),
(42, 'IN2021070042', 14, 127600, 0, 127600, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 18:18:10', 1),
(43, 'IN2021070043', 14, 97700, 0, 97700, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 18:19:01', 1),
(44, 'IN2021070044', 14, 76800, 0, 76800, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 18:22:12', 1),
(45, 'IN2021070045', 14, 127600, 0, 127600, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 18:25:07', 1),
(46, 'IN2021070046', 14, 104121, 0, 104121, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 18:26:09', 1),
(47, 'IN2021070047', 14, 127600, 0, 127600, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 18:30:27', 1),
(48, 'IN2021070048', 14, 0, 0, 0, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 18:33:23', 0),
(49, 'IN2021070049', 14, 127600, 0, 127600, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 18:33:33', 1),
(50, 'IN2021070050', 14, 101700, 0, 101700, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 18:34:13', 1),
(51, 'IN2021070051', 14, 95800, 0, 95800, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 18:36:19', 1),
(52, 'IN2021070052', 14, 95800, 0, 95800, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 18:39:02', 1),
(53, 'IN2021070053', 14, 127600, 0, 127600, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-21 18:42:48', 1),
(54, 'IN2021070054', 14, 55521, 0, 55521, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-27 20:27:00', 1),
(55, 'IN2021070055', 14, 108000, 0, 108000, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', '線上刷卡', '2021-07-29 11:35:40', 1),
(56, 'IN2021070056', 14, 104121, 0, 104121, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-30 12:59:04', 1),
(57, 'IN2021070057', 14, 166563, 0, 166563, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-31 08:43:07', 1),
(58, 'IN2021070058', 14, 159642, 0, 159642, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-07-31 13:19:45', 1),
(59, 'IN2021080001', 14, 55521, 0, 55521, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-08-02 08:30:46', 0),
(60, 'IN2021080002', 14, 55521, 0, 55521, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-08-02 08:35:56', 0),
(61, 'IN2021080003', 14, 189700, 0, 189700, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-08-02 08:49:08', 0),
(62, 'IN2021080004', 14, 39800, 500, 40300, 'pooh3838', 'ruby004949@gmail.com', '台北市敦化南路93號5樓', '0970619427', 'ATM匯款', '2021-08-03 12:21:33', 1);

-- --------------------------------------------------------

--
-- 資料表結構 `phplove`
--

CREATE TABLE `phplove` (
  `love_id` int(20) NOT NULL,
  `product_id` int(20) NOT NULL,
  `love_or_hate` int(2) NOT NULL,
  `memberid` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 資料表的匯出資料 `phplove`
--

INSERT INTO `phplove` (`love_id`, `product_id`, `love_or_hate`, `memberid`) VALUES
(354, 20, 1, 14),
(355, 19, 1, 14),
(356, 16, 1, 14),
(357, 15, 1, 14),
(358, 14, 1, 14),
(359, 11, 1, 14),
(360, 10, 1, 14),
(361, 9, 1, 14),
(362, 8, 1, 14),
(363, 7, 1, 14),
(367, 19, 1, 13),
(368, 16, 1, 13),
(369, 15, 1, 13),
(370, 14, 1, 13),
(371, 10, 1, 13),
(372, 11, 1, 13),
(373, 12, 1, 13),
(382, 9, 1, 18),
(383, 8, 1, 18),
(384, 7, 1, 18),
(385, 6, 1, 18),
(386, 5, 1, 18),
(387, 4, 1, 18),
(388, 3, 1, 18),
(389, 2, 1, 18);

-- --------------------------------------------------------

--
-- 資料表結構 `product`
--

CREATE TABLE `product` (
  `productid` int(11) UNSIGNED NOT NULL,
  `categoryid` int(11) UNSIGNED NOT NULL,
  `productname` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `productprice` int(11) UNSIGNED DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `p_amount` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 資料表的匯出資料 `product`
--

INSERT INTO `product` (`productid`, `categoryid`, `productname`, `productprice`, `description`, `p_amount`) VALUES
(1, 1, 'ASUS W5GT24DD', 54800, '◆ 1024MBDDRII雙通道記憶體\r\n◆ 100GB超大硬碟容量\r\n◆ 內建130萬畫素網路攝影機\r\n◆ 12吋鏡面寬螢幕', 20),
(2, 1, 'ASUS F3APT24DD', 51800, '◆ Intel雙核T2400\r\n◆ ATi獨立顯示TC256\r\n◆ 15.4吋鏡面寬螢幕\r\n◆ 100G-SATA\r\n◆ DVDSuperMulti(DL)', 18),
(3, 1, 'ASUS W7J', 58800, '◆ 雙核IntelDualCoreT2400處理器1.83G\r\n◆ Intel945PM高效晶片\r\n◆ nV獨立顯示256MB\r\n◆ 13吋鏡面寬螢', 11),
(4, 1, 'ASUS S6F', 68800, '◆ 11.1吋鏡面寬螢幕\r\n◆ 內建1GBDDRII記憶體\r\n◆ 雙核心低電壓超省電CPU\r\n◆ 大容量120GB\r\n◆ 附贈真皮保證書與真皮光學滑鼠', 10),
(5, 1, 'ASUS VX1', 108800, '◆ 師法藍寶堅尼跑車俐落線條美學\r\n◆ 15吋高解析鏡面螢幕1400x1050\r\n◆ 頂級獨立顯示NV7400VX-512MB\r\n◆ Intel雙核心T2500', 16),
(6, 2, 'ACER 3628A', 26900, '◆ 14.1吋鏡面寬螢幕\r\n◆ 內建DVD燒錄機SuperMuti\r\n◆ 超輕巧2.35KG\r\n◆ IntelPentium1.7G', 20),
(7, 2, 'ACER TM2403', 22900, '◆ 14.1吋寬螢幕\r\n◆ Intel新一代910GML晶片,DDR2記憶體\r\n◆ CeleronM最新超值奈米機\r\n◆ 創新FOLIO造型', 20),
(8, 2, 'ACER 5562', 48900, '◆ IntelCoreDuo雙核心\r\n◆ ATIX1400512MB3D顯示\r\n◆ 旋轉式130萬視訊\r\n◆ 抽取式藍芽網路話機', 19),
(9, 2, 'ACER Ferrari 4002WLMi', 48800, '◆ 採用AMDTurion64全新64位元處理器\r\n◆ 上蓋碳纖維材質\r\n◆ ATIX700獨立顯示\r\n◆ 內建藍芽', 17),
(10, 2, 'ACER 3022WTMi', 52900, '◆ 1.5kg輕巧靈動A4大小\r\n◆ 超效IntelCoreDuo雙核心\r\n◆ 130萬畫素225度網路視訊\r\n◆ 藍芽無線傳輸', 18),
(11, 3, 'HP TC4200', 57900, '◆ 可拆式旋轉鍵盤設計\r\n◆ 採用強化玻璃\r\n◆ 效能卓越、攜帶方便\r\n◆ 三年保固', 20),
(12, 3, 'HP NC2400', 55900, '◆ 超輕12吋WXGA\r\n◆ 鎂合金機身\r\n◆ 內建指紋辨識器\r\n◆ 內建光碟1.5KG', 18),
(13, 3, 'HP V2632', 39900, '◆ 14吋鏡面寬螢幕\r\n◆ Intel雙核心T2400\r\n◆ 80GB-SATA超大硬碟\r\n◆ 藍芽技術', 14),
(14, 3, 'HP Presario B2821', 36900, '◆ ATIX600獨立128MB顯示晶片\r\n◆ 白色鋼琴烤漆外觀\r\n◆ BrightView超亮顯示屏\r\n◆ 輕薄便攜僅重2.0kg', 17),
(15, 3, 'HP Pavilion dv4213AP', 39900, '◆ 15.4吋鏡面高亮度寬螢幕\r\n◆ ATIMobilityX700獨立128MB\r\n◆ 內建6合1數位讀卡機\r\n◆ 三年保固', 86),
(16, 4, 'SONY VGN-AR18TP', 149800, '◆ Intel雙核心T2600\r\n◆ NVIGF7600GT256MB\r\n◆ 160超大SATA硬碟\r\n◆ 17吋1920X1200高畫質\r\n◆ 藍光燒錄', 79),
(17, 4, 'SONY VAIO VGN-FJ79TP', 39800, '◆ IntelPM2G處理器\r\n◆ 14吋寬螢\r\n◆ 80GBSATA大硬碟\r\n◆ DVD雙層燒錄\r\n◆ 專業版XPP', 92),
(18, 4, 'SONY VGN-FE25TP', 49800, ' Intel雙核心T2400\r\n 80GBSATA大硬碟\r\n NVIGF7400獨立顯示256MB\r\n 15.4吋2.85KG', 106),
(19, 2, 'Acer SF514-55GT-53NK', 33900, '處理器：Intel® Core™ i5-1135G7\r\n顯示晶片：NVIDIA MX350 2G\r\n記憶體：16GB LPDDR4X(Onboard)\r\n硬碟：512GB PCIe NVMe SSD\r\n螢幕：14\\\\\\" FHD/鏡面/LED背光/IPS/觸控\r\n無線網路：802.11a/b/g/n/acR2+ax/2x2 MU-MIMO\r\n其他：Type-C、HDMI、指紋辨識\r\n軟體：Windows 10 Home\r\n重量：約1050g', 100),
(20, 44, 'MacBook Pro', 39900, 'Apple M1 晶片配備 8 核心 CPU、8 核心 GPU 與 16 核心神經網路引擎\r\n8GB 統一記憶體\r\n256GB SSD 儲存裝置\r\n具備原彩顯示的 13 吋 Retina 顯示器\r\n巧控鍵盤\r\n觸控列和 Touch ID\r\n力度觸控板\r\n兩個 Thunderbolt / USB 4 埠', 100),
(21, 44, 'MacBook Air', 30900, 'Apple M1 晶片配備 8 核心 CPU、7 核心 GPU 與 16 核心神經網路引擎\r\n8GB 統一記憶體\r\n256GB SSD 儲存裝置\r\n具備原彩顯示的 Retina 顯示器\r\n巧控鍵盤\r\nTouch ID\r\n力度觸控板\r\n兩個 Thunderbolt / USB 4 埠', 100),
(22, 44, 'iMac', 57900, '3.1GHz 6 核心第 10 代 Intel Core i5 處理器\r\nTurbo Boost 最高可達 4.5GHz\r\n8GB 2666MHz DDR4 記憶體，可訂製最多達 128GB\r\n256GB SSD 儲存裝置\r\nRadeon Pro 5300 配備 4GB GDDR6 記憶體\r\n兩個 Thunderbolt 3 埠\r\nRetina 5K 5120 x 2880 P3 顯示器具備原彩顯示技術 ', 100),
(23, 39, 'MSI GP66 Leopard 11UG-007TW', 61900, '搭載最新第 11 代Intel Core i7 處理器\r\n搭載NVIDIA GeForce RTX 3070 筆記型電腦 GPU\r\n搭載15.6吋高畫質(1920x1080), 240Hz更新率, IPS等級電競面板\r\n全新Cooler Boost 5全新酷涼散熱系統， 搭配2個風扇與6個散熱導管\r\nSteelSeries單鍵RGB全彩背光鍵盤\r\nMSI Center軟體，提供獨家電競模式\r\nMSI App Player 軟體，用電競筆電暢玩手遊\r\n最新Wi-Fi 6E極速無線網路體驗\r\n支援播放高解析音樂', 20);

-- --------------------------------------------------------

--
-- 資料表結構 `product_image`
--

CREATE TABLE `product_image` (
  `picture_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_url` varchar(200) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 資料表的匯出資料 `product_image`
--

INSERT INTO `product_image` (`picture_id`, `product_id`, `image_url`) VALUES
(1, 1, 'W5GT24DD.jpg'),
(2, 2, 'F3APT24DD.jpg'),
(3, 3, 'W7J.jpg'),
(4, 4, 'S6F.jpg'),
(5, 5, 'VX1.jpg'),
(6, 6, '3628A.jpg'),
(7, 7, 'TM2403.jpg'),
(8, 8, '5562.jpg'),
(9, 9, '4002WLMi.jpg'),
(10, 10, '3022WTMi.jpg'),
(11, 11, 'TC4200.jpg'),
(12, 12, 'NC2400.jpg'),
(13, 13, 'V2632.jpg'),
(14, 14, 'B2821.jpg'),
(15, 15, 'dv4213AP.jpg'),
(16, 16, 'VGN-AR18TP.jpg'),
(17, 17, 'VGN-FJ79TP.jpg'),
(18, 18, 'VGN-FE25TP.jpg'),
(222, 20, 'mbp-silver-gallery4-removebg-preview.png'),
(223, 20, 'mbp-silver-gallery2-202011-removebg-preview.png'),
(224, 20, 'mbp-silver-gallery3-removebg-preview.png'),
(225, 20, 'mbp-silver-gallery1-202011-removebg-preview.png'),
(226, 21, 'macbook-air-gallery3.jpg'),
(227, 21, 'macbook-air-gallery2.jpg'),
(228, 21, 'macbook-air-gallery1.jpg'),
(229, 21, 'macbook-air-gallery4.jpg'),
(230, 22, 'imac-27-gallery-1.jpg'),
(231, 22, 'imac-27-gallery-2.jpg'),
(232, 22, 'imac-27-gallery-3.jpg'),
(233, 22, 'imac-27-gallery-4.jpg'),
(234, 19, 'SF514-55GT-53NK-1.jpg'),
(235, 19, 'SF514-55GT-53NK-2.jpg'),
(236, 19, 'SF514-55GT-53NK-3.jpg'),
(237, 19, 'SF514-55GT-53NK-4.jpg'),
(238, 19, 'SF514-55GT-53NK-5.jpg'),
(239, 19, 'SF514-55GT-53NK-6.jpg'),
(240, 19, 'SF514-55GT-53NK-7.jpg'),
(241, 23, 'GP66-11UG-1.png'),
(242, 23, 'GP66-11UG-2.png'),
(243, 23, 'GP66-11UG-3.png'),
(244, 23, 'GP66-11UG-4.png'),
(245, 23, 'GP66-11UG-5.png');

--
-- 已匯出資料表的索引
--

--
-- 資料表索引 `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`categoryid`);

--
-- 資料表索引 `memberdata`
--
ALTER TABLE `memberdata`
  ADD PRIMARY KEY (`m_id`),
  ADD UNIQUE KEY `m_username` (`m_username`);

--
-- 資料表索引 `orderdetail`
--
ALTER TABLE `orderdetail`
  ADD PRIMARY KEY (`orderdetailid`);

--
-- 資料表索引 `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`orderid`);

--
-- 資料表索引 `phplove`
--
ALTER TABLE `phplove`
  ADD PRIMARY KEY (`love_id`);

--
-- 資料表索引 `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`productid`);

--
-- 資料表索引 `product_image`
--
ALTER TABLE `product_image`
  ADD PRIMARY KEY (`picture_id`);

--
-- 在匯出的資料表使用 AUTO_INCREMENT
--

--
-- 使用資料表 AUTO_INCREMENT `category`
--
ALTER TABLE `category`
  MODIFY `categoryid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;
--
-- 使用資料表 AUTO_INCREMENT `memberdata`
--
ALTER TABLE `memberdata`
  MODIFY `m_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- 使用資料表 AUTO_INCREMENT `orderdetail`
--
ALTER TABLE `orderdetail`
  MODIFY `orderdetailid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;
--
-- 使用資料表 AUTO_INCREMENT `orders`
--
ALTER TABLE `orders`
  MODIFY `orderid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;
--
-- 使用資料表 AUTO_INCREMENT `phplove`
--
ALTER TABLE `phplove`
  MODIFY `love_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=391;
--
-- 使用資料表 AUTO_INCREMENT `product`
--
ALTER TABLE `product`
  MODIFY `productid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;
--
-- 使用資料表 AUTO_INCREMENT `product_image`
--
ALTER TABLE `product_image`
  MODIFY `picture_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=246;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
