-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-04-15 05:50:06
-- 服务器版本： 5.5.39
-- PHP Version: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `test8000`
--

-- --------------------------------------------------------

--
-- 表的结构 `weixin_print_temp`
--

CREATE TABLE IF NOT EXISTS `weixin_print_temp` (
`id` int(11) NOT NULL COMMENT '模板ID',
  `print_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '模板名称',
  `paper_width` int(11) NOT NULL DEFAULT '0' COMMENT '快递单模板宽度',
  `paper_height` int(11) NOT NULL DEFAULT '0' COMMENT '快递单模板高度',
  `base_temp_img` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '底部快递单模板',
  `items_params` varchar(2000) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '订单打印参数列表'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `weixin_print_temp`
--
ALTER TABLE `weixin_print_temp`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `weixin_print_temp`
--
ALTER TABLE `weixin_print_temp`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '模板ID',AUTO_INCREMENT=6;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
