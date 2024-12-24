-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 23, 2024 at 09:54 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `supplier_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `expense_types`
--

CREATE TABLE `expense_types` (
  `id` int(11) NOT NULL,
  `type_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expense_types`
--

INSERT INTO `expense_types` (`id`, `type_name`) VALUES
(20, 'أكياس'),
(7, 'ا/ احمد الحسيني'),
(21, 'اطباق'),
(10, 'اكراميات'),
(22, 'البيئة'),
(12, 'الشيخ تامر'),
(16, 'الصحه'),
(11, 'ايجار الشقة'),
(19, 'ايجار المحل'),
(15, 'ايجار المخزن'),
(14, 'بقسماط'),
(8, 'بهارات'),
(4, 'حي الطالبيه'),
(3, 'كهرباء'),
(5, 'كهرباء الورداني'),
(9, 'متنوع'),
(13, 'مصطفي الصواف'),
(6, 'منظفات'),
(18, 'مياه'),
(17, 'ورق باركود');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `type` enum('اجل','تحصيل','كاش') NOT NULL,
  `invoice_date` date DEFAULT NULL,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `misc_expenses`
--

CREATE TABLE `misc_expenses` (
  `id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `expense_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`) VALUES
(839, 'أبو الولد'),
(820, 'أبو يوسف'),
(803, 'أرباح جملة'),
(835, 'أولاد خلف'),
(792, 'أولاد علام'),
(754, 'إبراهيم سعيد'),
(866, 'ابلكيشن'),
(888, 'ابي ميلك'),
(765, 'احمد بدوي'),
(845, 'احمد ناجي'),
(794, 'ادمي'),
(804, 'ازيس'),
(774, 'اسبيرو سباتس'),
(865, 'اشرف شنط'),
(757, 'اطياب'),
(812, 'اكوافينا'),
(830, 'الاريج'),
(863, 'الاكسلانس'),
(772, 'البوادي'),
(837, 'التقوي'),
(897, 'الراوي'),
(838, 'السلام جملة'),
(890, 'السنبلة'),
(884, 'الشاهين'),
(875, 'الشعيبي'),
(864, 'الشيخ عبدالرحمن'),
(834, 'الشيخ مصطفي'),
(766, 'الشيخ ياسر'),
(761, 'الضحي'),
(740, 'الطحان'),
(867, 'العزيزي'),
(870, 'العمدة'),
(741, 'الفجر'),
(832, 'الفرسان'),
(873, 'القبطان'),
(730, 'الكوثر جملة'),
(825, 'المراعي'),
(734, 'المراعي زبادي'),
(874, 'المراعي عصير'),
(801, 'المراعي لبن'),
(842, 'المصرية'),
(732, 'الهلاوي'),
(743, 'الوادي'),
(782, 'الوطنية مجمدات'),
(738, 'اندومي'),
(827, 'اولكر'),
(805, 'ايديتا'),
(887, 'ايزيس'),
(807, 'ايفري داي'),
(869, 'ايمن لبن'),
(813, 'باتيه'),
(847, 'بافس'),
(816, 'باندا'),
(775, 'برانش'),
(881, 'بريزيدون'),
(759, 'بزياده'),
(809, 'بسكوت بيكيز'),
(791, 'بسكوت رينكو'),
(733, 'بسمة خضروات'),
(851, 'بطاطس فارم'),
(862, 'بلو بلس'),
(846, 'بن أبو طالب'),
(852, 'بن أبو عوف'),
(893, 'بن الشيخ'),
(787, 'بن الفلاح'),
(859, 'بيبسي'),
(786, 'بيج كولا'),
(831, 'تامر حلويات'),
(745, 'تايجر'),
(802, 'تبارك'),
(855, 'توتس'),
(760, 'تويز سيتي'),
(856, 'جاجور'),
(898, 'جلوب وان'),
(744, 'جو بايتس'),
(817, 'جيلي كاندي'),
(895, 'حسين الصراف'),
(769, 'حلاوة الرشيدي'),
(843, 'حلاوة الفارس'),
(798, 'حلاوه طحنيه'),
(789, 'حلويات الأصيل'),
(810, 'حلويات الضحي'),
(788, 'حلويات العبد'),
(778, 'حلويات مصنعات تلاجه'),
(879, 'خوشالة'),
(868, 'دابل دير'),
(736, 'دانون'),
(797, 'دانيت'),
(763, 'دومتي'),
(752, 'دومتي باتيه'),
(885, 'ديجستيف'),
(747, 'ذكي شبسي'),
(725, 'رافت بيض'),
(724, 'ربيع مخللات'),
(773, 'رشيدي الميزان'),
(883, 'رنجه'),
(849, 'روجينا'),
(770, 'رودس'),
(828, 'رولانا'),
(886, 'رونيسكا'),
(726, 'ريتش بيك'),
(824, 'ريدبول'),
(850, 'زاد'),
(748, 'زبادي بلدي'),
(737, 'زبادي جهينه'),
(878, 'سعد'),
(876, 'سنيور'),
(854, 'شاى العروسة'),
(781, 'شبيسيكو'),
(871, 'شنايدر'),
(767, 'شيبسي اسبادس'),
(808, 'شيبسي سولو'),
(896, 'شيبسي ليون'),
(891, 'شيدر'),
(836, 'صولو'),
(899, 'طلبيه'),
(751, 'عاصم ايسكريم'),
(758, 'عبدالله حلويات'),
(783, 'عبدالله لبن'),
(815, 'عبورلاند'),
(806, 'عسل اورجانيك'),
(777, 'عسل خير بلدنا'),
(796, 'عسل متنوع'),
(823, 'عسل مرام'),
(793, 'عصير الراوي'),
(762, 'عصير كارفن'),
(728, 'فان داي'),
(844, 'فاين فودز'),
(833, 'فلامنجو'),
(756, 'فور ايفر'),
(821, 'فوكس'),
(819, 'فولت'),
(882, 'في كولا'),
(800, 'فيتراك'),
(861, 'فيرن'),
(811, 'كادبوري'),
(900, 'كاندي'),
(872, 'كرولس بيض'),
(785, 'كريستال'),
(746, 'كشكول جملة'),
(857, 'كنور'),
(814, 'كنوز'),
(840, 'كواليتي'),
(776, 'كوفي بريك'),
(848, 'كوكاكولا'),
(822, 'كوكس'),
(768, 'كوكي'),
(829, 'كوليتي'),
(841, 'كيكه تيتي'),
(799, 'لافاش'),
(729, 'لايك ميلك'),
(739, 'لبن جهينه'),
(727, 'لبن وائل'),
(860, 'لمار'),
(795, 'لوليتا'),
(858, 'ليبتون'),
(853, 'ماجي'),
(880, 'ماكس كولا'),
(749, 'متنوع'),
(750, 'محمد الزغبي'),
(753, 'محمد حسين'),
(771, 'محمد حلويات'),
(764, 'محمد سمير'),
(755, 'محمود حماده'),
(894, 'محمود كازبلانكا'),
(735, 'مزارع دينا'),
(818, 'مصطفي حلويات'),
(731, 'مصطفي مجمدات'),
(889, 'مهدي'),
(892, 'مولتو'),
(826, 'مونتانا'),
(779, 'مونجيني'),
(742, 'نسله'),
(780, 'نودلز'),
(877, 'هيسلي'),
(784, 'ويلز'),
(790, 'ويندوز');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `expense_types`
--
ALTER TABLE `expense_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type` (`type_name`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `misc_expenses`
--
ALTER TABLE `misc_expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type_id` (`type_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `expense_types`
--
ALTER TABLE `expense_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `misc_expenses`
--
ALTER TABLE `misc_expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=901;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Constraints for table `misc_expenses`
--
ALTER TABLE `misc_expenses`
  ADD CONSTRAINT `misc_expenses_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `expense_types` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
