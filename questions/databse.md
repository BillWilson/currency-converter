# Database 

## Question 1
- 對 `rooms.property_id` 與 `orders.room_id` 建立 index
- 在 `orders` 新增 room 的 sku id (format: `(PROPERTY_NAME)_(ROOM_SKU)`, Ex: `PROABC_ROOMB`), 並對其建立 index。 直接針對 sku 中的 `property_name` 進行 GROUP BY (Ex: `GROUP BY left(orders.room_sku,6)`)

## Question 2
- 沒有用上正確的 index 導致 full table scan, 使用 `EXPLAIN ANALYSE` 去檢查分析出的結果，根據結果最佳化查詢成本
- 資料量大 (>10M)，針對資料存取頻率分級儲存(依照不同年份分開 or 一年以上資料單獨儲存)，以確保查詢資料的範圍在限定的時間區間
