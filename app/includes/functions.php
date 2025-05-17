<?php
function searchProducts($db, $userCity, $userDistrict, $keyword = '', $offset = 0, $limit = 4) {
  $keyword = "%$keyword%";

  $sql = "SELECT p.*, u.city, u.district, u.name AS seller_name
          FROM products p
          JOIN users u ON p.user_id = u.id
          WHERE u.city = :city
            AND p.title LIKE :keyword
            AND p.expiration_date >= :today
            AND p.stock > 0
          ORDER BY (u.district = :district) DESC, p.created_at DESC
          LIMIT :offset, :limit";

  $stmt = $db->prepare($sql);
  $stmt->bindValue(':city', $userCity, PDO::PARAM_STR);
  $stmt->bindValue(':district', $userDistrict, PDO::PARAM_STR);
  $stmt->bindValue(':keyword', $keyword, PDO::PARAM_STR);
  $stmt->bindValue(':today', date('Y-m-d'), PDO::PARAM_STR);
  $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
  $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
  $stmt->execute();

  $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $count_sql = "SELECT COUNT(*) 
                FROM products p 
                JOIN users u ON p.user_id = u.id 
                WHERE u.city = :city 
                  AND p.title LIKE :keyword
                  AND p.expiration_date >= :today
                  AND p.stock > 0";

  $count_stmt = $db->prepare($count_sql);
  $count_stmt->bindValue(':city', $userCity, PDO::PARAM_STR);
  $count_stmt->bindValue(':keyword', $keyword, PDO::PARAM_STR);
  $count_stmt->bindValue(':today', date('Y-m-d'), PDO::PARAM_STR);
  $count_stmt->execute();
  $total_products = $count_stmt->fetchColumn();

  $total_pages = ceil($total_products / $limit);

  return [$products, $total_pages];
}

