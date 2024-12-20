# TEST ANSWER

## SQL TEST 1
```bash
SELECT
    o.bnb_id,
    b.name AS bnb_name,
    SUM(o.amount) AS may_amount
FROM
    orders o
JOIN
    bnbs b ON o.bnb_id = b.id
WHERE
    o.currency = 'TWD'
    AND o.created_at >= '2023-05-01 00:00:00'
    AND o.created_at < '2023-06-01 00:00:00'
GROUP BY
    o.bnb_id, b.name
ORDER BY
    may_amount DESC
LIMIT 10;
```

## SQL TEST 2 （順序越前越優先考慮）
### 1 確認是否為索引缺失的問題
```bash
EXPLAIN
SELECT
    o.bnb_id,
    b.name AS bnb_name,
    SUM(o.amount) AS may_amount
FROM
    orders o
JOIN
    bnbs b ON o.bnb_id = b.id
WHERE
     o.currency = 'TWD'
     AND o.created_at >= '2023-05-01 00:00:00'
     AND o.created_at < '2023-06-01 00:00:00'
GROUP BY
    o.bnb_id, b.name
ORDER BY
    may_amount DESC
LIMIT 10;
```
1.判斷是否缺乏索引
- 查詢 EXPLAIN 結果，檢查 key=NULL 或 Using join buffer。
- 如果 type=ALL 或 rows 數量過高，意味著需要新增索引。

2.新增語法
- bnbs 表（若該表沒有primary key 正常不會發生)：
```bash
CREATE INDEX idx_bnbs_id ON bnbs (id);
```
- orders 表：
```bash
CREATE INDEX idx_orders_bnb_id ON orders (bnb_id);
CREATE INDEX idx_orders_composite ON orders (bnb_id, currency, created_at);
```
### 2 使用子查詢 : 提前過濾符合條件的資料，減少 JOIN 和分組的數據量
```bash
SELECT
    o.bnb_id,
    b.name AS bnb_name,
    SUM(o.amount) AS may_amount
FROM
    (SELECT bnb_id, amount
     FROM orders
     WHERE currency = 'TWD'
       AND created_at >= '2023-05-01 00:00:00'
       AND created_at < '2023-06-01 00:00:00') AS filtered_orders
JOIN
    bnbs b ON filtered_orders.bnb_id = b.id
GROUP BY
    o.bnb_id, b.name
ORDER BY
    may_amount DESC
LIMIT 10;
```
### 分析
- 優點：
  - 當資料篩選條件能顯著減少數據量時，子查詢有助於優化效能。
- 缺點：
  - 子查詢的結果可能需要存放在臨時表或記憶體中，增加IO與記憶體成本。
  - 子查詢可能導致MySQL優化器不可以運作。

### 3 對資料庫進行讀寫分離設計
- 將主庫 (Master) 專注於處理寫入操作，例如新增訂單。
- 將從庫 (Replica) 用於查詢操作，例如統計金額和篩選訂單。

### 分析
- 優點
  - 提升性能：主庫專注寫入，從庫分擔讀取壓力。
  - 高可用性：從庫作為備份，主庫故障時快速切換。
- 缺點
  - 數據延遲：從庫同步可能有延遲，影響即時性。
  - 系統更複雜：增加開發與維護成本。

### 4 預計算：提前統計每月的金額，將結果存儲在專用的報表中。
實現方式：
1.建立每月報表
2.定期批量更新
3.查詢簡化
- 優點：
  - 高效查詢：直接使用計算結果，避免每次重新計算，在計算結果可供多處報表使用時相當有幫助


### 5 對資料表進行分區,如果資料量非常大 Ex:大於百萬以上

- 建立新分區表
```bash
CREATE TABLE orders_partitioned (
    id INT NOT NULL,
    bnb_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    currency CHAR(3) NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id, created_at)
)
PARTITION BY RANGE (YEAR(created_at) * 100 + MONTH(created_at)) (
    PARTITION p202301 VALUES LESS THAN (202302),  -- 2023年1月
    PARTITION p202302 VALUES LESS THAN (202303),  -- 2023年2月
    PARTITION p202303 VALUES LESS THAN (202304),  -- 2023年3月
    PARTITION pmax VALUES LESS THAN MAXVALUE      -- 其他月份
);
```
- 將現有資料遷移到分區表
```bash
INSERT INTO orders_partitioned (id, bnb_id, amount, currency, created_at)
SELECT id, bnb_id, amount, currency, created_at
FROM orders;
```
- 備份表並切換表名：
```bash
RENAME TABLE orders TO orders_backup;
RENAME TABLE orders_partitioned TO orders;
```
### 分析
- 優點：
  - 提升針對分區鍵的查詢效能。

- 缺點：
  - 查詢必須包含分區鍵才能享受效能提升。
  - 管理較複雜，寫入性能可能受到影響。
  - 索引僅適用於單一分區，跨分區查詢效能可能下降。

---
# API 實作測驗
## Prerequisites

Ensure the following tools are installed:
- **Docker** and **Docker Compose**
- **Git**

## Project Setup

1. **Clone the repository**:
   ```bash
   git clone https://github.com/amattsuyolo/AsiaYoTestAnswer.git
   cd AsiaYoTestAnswer

2. Start the Docker containers:
   ```bash
   docker-compose up -d

3. Enter the Laravel container:
    ```bash
    docker exec -it laravel-app bash
4. Install dependencies and set up the application:
    ```bash
    cd laravel
    composer install
    cp .env.example .env
    php artisan key:generate
5. Access the application by curl
   ```
   curl -X POST \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -d '{
           "id": "A0000001",
           "name": "Melody Holiday Inn",
           "address": {
               "city": "taipei-city",
               "district": "da-an-district",
               "street": "fuxing-south-road"
           },
           "price": "1950",
           "currency": "TWD"
         }' \
     http://127.0.0.1:8000/api/orders
   ```

6. Run test
   ```
   php artisan test tests/Feature/OrderApiTest.php
   ```

## 專案說明

此專案透過Laravel典型MVC架構進行開發，並透過下列方法確保程式碼品質與維護性：

### SOLID 原則:
1. Single Responsibility Principle (SRP)
- OrderRequest 負責驗證輸入格式
- OrderService 專注於業務邏輯處理
- OrderData (DTO) 用於定義、封裝訂單資料結構

2. Open-Closed Principle (OCP)
- 新增其他貨幣支援時，只需新增對應的 Converter，不需修改現有程式碼。

3. Interface Segregation Principle (ISP)
- 以 OrderServiceInterface、CurrencyConverterInterface 清晰定義介面，使得呼叫端僅依賴必要的行為，不需面對不相關的功能。

4. Dependency Inversion Principle (DIP)
- Controller 依賴 OrderServiceInterface 而非 OrderService 實作類別，並透過 Service Container 綁定介面到實作。
- OrderService 不直接 new 出 Converter 實例，而是依賴 CurrencyConverterResolverInterface（例如 CurrencyConverterResolver）來取得適當的 Converter 實例。如此，Service 只依賴抽象介面，不依賴具體實作。

### 設計模式:
- Strategy Pattern (策略模式)：

貨幣轉換行為使用策略模式實作。OrderService 透過 CurrencyConverterResolver 根據不同 currency 取得對應的 Converter（策略）來執行轉換。
這使得未來要新增其他貨幣只需新增新的 Converter 實作類別並在 Resolver 中處理，不需修改核心業務邏輯程式碼。