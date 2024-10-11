-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 04, 2024 lúc 09:11 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `shoppingcart`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(50) NOT NULL,
  `customer_phone` varchar(12) NOT NULL,
  `customer_address` varchar(255) NOT NULL,
  `date_added` datetime DEFAULT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `customer_name`, `customer_phone`, `customer_address`, `date_added`, `status`) VALUES
(1, 'Vũ Tuấn Anh', '0964444444', 'aaaaaaaaaaa', '2024-10-02 00:00:00', 5);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_detail`
--

CREATE TABLE `order_detail` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order_detail`
--

INSERT INTO `order_detail` (`id`, `order_id`, `product_id`, `quantity`) VALUES
(1, 1, 7, 1),
(2, 1, 10, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `price` bigint(12) NOT NULL,
  `rrp` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `img` text DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `category` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `title`, `description`, `price`, `rrp`, `quantity`, `img`, `date_added`, `category`) VALUES
(7, 'Bún gạo lức tím update 4353', 'Rice noodles made from brown rice – Chewy, sweet, and fragrant. Versatile for a variety of dishes.\r\n - Supports excellent weight loss.\r\nConvenient and luxurious packaging (suitable for gifting).\r\n - Easy to store.\r\n - Provides energy for the body.\r\n\r\nIngredients: Brown rice is rich in protein, iron, vitamins (especially vitamin B1), calcium, potassium, etc., making it an ideal food for nourishing the body. Suitable for the elderly, children, and women. This type of rice has high nutritional value, can be ground into powder for baby food, and cooked into porridge with meat and vegetables, providing a rich source of energy and micronutrients to help children eat well and grow quickly.\r\n\r\nUsage: Soak the noodles in cold water for about 5-10 minutes. Boil in hot water for about 4-6 minutes, then drain and rinse with clean water, depending on the intended use: stir-fry with shrimp, crab, meat, eggs, vegetables, and seasonings... Cook with broth, chicken, duck, pork... Use with various hot pots or enjoy as fresh noodles.', 40, 35, 100, 'bun-gao-lut-tim.jpg', '2024-09-23 23:08:49', 'noodles'),
(8, 'Bún gạo trắng update 1', 'Sợi bún tròn, nhỏ, màu trắng dễ nấu và chế biến thấm gia vị. Không sử dụng hàn the, không sử dụng phụ gia khác, tiện lợi, dễ bảo quản.\r\nCách dùng\r\nBÚN GẠO XÀO – Ngâm bún trong nước nóng sôi (100 độ C) khoảng 02 phút, vớt ra xả nước lạnh, để ráo nước. Xào tôm, thịt, rau củ,… và các gia vị cho vừa ăn, cho bún vào đảo đều cho nóng là dùng được.\r\nBÚN GẠO TƯƠI – Ngâm bún trong nước sôi (100 độ C) khoảng 06 phút, vớt ra xả nước lạnh, để ráo nước khoảng 10 phút. Có thể dùng với các loại lẩu, bún thịt nướng hoặc dùng cuốn gỏi cuốn, bì cuốn,…\r\nBảo quản\r\nĐể nơi khô mát, tránh ánh nắng trực tiếp và nhiệt độ cao\r\nSản xuất tại Việt Nam', 34, 30, 100, 'bun-gao-trang.jpg', '2024-09-23 23:08:49', 'noodles'),
(9, 'Phở Gạo Trắng', 'Mô tả\r\nSản phẩm được làm từ nguyên liệu chọn lọc, nguyên chất. Sản xuất bằng máy tự động, công nghệ tiên tiến, sấy khô trên dây truyền khép kín. Vắt phở sau khi trụng qua nước sôi cho ra sợi phở trắng, dai, mềm, không bị cứng, khô. Siêu tiện lợi cho các bà nội trợ chuẩn bị các bữa ăn nhanh cho gia đình.\r\n\r\nXuất xứ: Việt Nam\r\n\r\nThành phần: 100% gạo trắng hữu cơ\r\n\r\nPhở gạo trắng làm từ giống gạo hữu cơ thơm ngon. Sợi phở dai rất ngon\r\n\r\nCách dùng: Rửa sạch, luộc qua nước sôi 7-10 phút và chế biến các món ăn tùy thích như phở trộn, phở xào rau củ, phở xào bò, phở bò, phở gà, …', 37, 32, 100, 'pho-gao-trang.jpg', '2024-09-23 23:08:49', 'noodles'),
(10, 'Bí Hạt Đậu Hữu Cơ DannyGreen', 'Mô tả\r\nVỊ NGỌT VƯỢT TRỘI\r\n\r\n– Giống: xuất xứ Hà Lan.\r\n\r\n– Hình dạng bên ngoài: vỏ màu vàng nhạt, có hình quả đậu phộng\r\n\r\n– Bên trong: ít hạt, thịt màu vàng cam, dẻo và ngọt.\r\n\r\nBí Hạt Đậu DannyGreen là một trong những nguyên liệu thơm ngon giàu dinh dưỡng của các món ăn như soup, canh, bí Hạt Đậu xào và sữa bí Hạt Đậu.\r\n\r\nNgoài ra loại quả này còn có thể được chế biến thành món ăn dặm bổ dưỡng cho trẻ nhỏ.', 104, 100, 100, 'bi-hat-dau.jpg', '2024-09-23 23:08:49', 'fruits'),
(11, 'Dưa Lê Hữu Cơ Bạch Kim', 'Dưa lê Bạch Kim/ Golden Pyriform-Melon, xuất xứ ĐàiLoan (giống dưa Kim Cô Nương).\r\n\r\n– Bên ngoài: trái hình elip, vỏ vàng tươi.\r\n\r\n– Bên trong: ruột trắng, hơi giòn, vị ngọt thanh.\r\n\r\nCanh tác theo phương thức hoàn toàn hữu cơ (organic).\r\n\r\nThành phần dinh dưỡng: cung cấp chất xơ, giàu vitamin C, A, B, folate…\r\n\r\nLợi ích: tốt cho mắt, tim mạch, phòng ngừa bệnh ung thư, tăng cường hệ miễn dịch, làm đẹp da…\r\n\r\nSản phẩm của chúng tôi “Đảm bảo nguồn thực phẩm sạch, an toàn cho gia đình bạn”, bật mí ăn sẽ ngon hơn khi để lạnh.', 132, 0, 100, 'dua-le-bach-kim.jpg', '2024-09-23 23:08:49', 'fruits'),
(12, 'Dưa Lê Hữu Cơ Hồng Kim', 'Mô tả\r\nDưa lê Hồng Kim DannyGreen/ Orange Golden Pyriform Melon, xuất xứ Thái Lan (giống dưa Kim Hồng Ngọc)\r\n\r\n– Bên ngoài: hình cầu, vỏ màu vàng chanh.\r\n\r\n– Bên trong: ruột cam, mùi thơm nhẹ, vị thanh, thịt rất giòn.\r\n\r\nCanh tác theo phương thức hoàn toàn hữu cơ (organic).\r\n\r\nThành phần dinh dưỡng: cung cấp chất xơ, giàu vitamin C, A, B, folate…\r\n\r\nLợi ích: tốt cho mắt, tim mạch, phòng ngừa bệnh ung thư, tăng cường hệ miễn dịch, làm đẹp da…\r\n\r\nSản phẩm của chúng tôi “Đảm bảo nguồn thực phẩm sạch, an toàn cho gia đình bạn”, bật mí ăn sẽ ngon hơn khi để lạnh.', 96, 0, 100, 'dua-le-hong-kim.jpg', '2024-09-23 23:08:49', 'fruits'),
(14, 'Dưa Lưới Hữu Cơ Biển Hoàng Gia', 'Dưa lưới Biển Hoàng Gia / SeaRoyal Musk-Melon là giống có xuất xứ Nhật Bản (giống dưa lưới Taki) – Bên ngoài: tròn đều, vỏ xanh nhạt có vân lưới nhẹ.\r\n\r\n– Bên trong: ruột cam, mềm, vị ngọt thanh tự nhiên. Canh tác theo phương thức hoàn toàn hữu cơ (organic). Thành phần dinh dưỡng: cung cấp chất xơ, giàu vitamin C, A, B, folate…\r\n\r\nLợi ích: tốt cho mắt, tim mạch, phòng ngừa bệnh ung thư, tăng cường hệ miễn dịch, làm đẹp da… Sản phẩm của chúng tôi “Đảm bảo nguồn thực phẩm sạch, an toàn cho gia đình bạn”, bật mí ăn sẽ ngon hơn khi để lạnh.', 155, 0, 100, 'dua-luoi-bien-hoang-gia.jpg', '2024-09-23 23:08:49', 'fruits'),
(15, 'Dưa Lưới Hữu Cơ Biển Kim Sa', 'aaaa', 133, 0, 100, 'dua-luoi-bien-kim-sa.jpg', NULL, 'fruits'),
(16, 'Dưa Lưới Hữu Cơ Biển Ngọc Bích', 'Dưa lưới Biển Ngọc Bích/ SeaEmerald Musk-Melon là giống có xuất xứ Nhật Bản (giống dưa lưới Ichiba)\r\n\r\nBên ngoài: elip, vỏ xanh đậm có vân lưới nhẹ.\r\n\r\nBên trong: ruột xanh, hơi giòn, dưa chín có mùi thơm vani nhẹ, vị ngọt thanh tự nhiên Canh tác theo phương thức hoàn toàn hữu cơ (organic). Thành phần dinh dưỡng: cung cấp chất xơ, giàu vitamin C, A, B, folate…\r\n\r\nLợi ích: tốt cho mắt, tim mạch, phòng ngừa bệnh ung thư, tăng cường hệ miễn dịch, làm đẹp da…\r\n\r\nSản phẩm của chúng tôi “Đảm bảo nguồn thực phẩm sạch, an toàn cho gia đình bạn”, bật mí ăn sẽ ngon hơn khi để lạnh.', 180, 0, 100, 'dua-luoi-bien-ngoc-bich.jpg', '2024-09-23 23:08:49', 'fruits');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `order_detail`
--
ALTER TABLE `order_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
