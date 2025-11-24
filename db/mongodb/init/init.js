// Switch to database
db = db.getSiblingDB('huflit_logs');

// Create collections with validation
db.createCollection('audit_logs', {
  validator: {
    $jsonSchema: {
      bsonType: "object",
      required: ["table", "operation", "timestamp", "site"],
      properties: {
        table: { bsonType: "string", description: "Table name (Khoa, SinhVien, etc.)" },
        operation: { enum: ["INSERT", "UPDATE", "DELETE"], description: "CRUD operation" },
        data: { bsonType: "object", description: "Data involved in operation" },
        old_data: { bsonType: "object", description: "Old data for UPDATE/DELETE" },
        timestamp: { bsonType: "date", description: "Operation timestamp" },
        site: { enum: ["Site_A", "Site_B", "Site_C", "Global"], description: "Database site" },
        ip_address: { bsonType: "string", description: "Client IP" },
        user_agent: { bsonType: "string", description: "Client user agent" }
      }
    }
  }
});

db.createCollection('query_history', {
  validator: {
    $jsonSchema: {
      bsonType: "object",
      required: ["endpoint", "method", "timestamp"],
      properties: {
        endpoint: { bsonType: "string", description: "API endpoint" },
        method: { enum: ["GET", "POST", "PUT", "DELETE"], description: "HTTP method" },
        params: { bsonType: "object", description: "Query parameters" },
        body: { bsonType: "object", description: "Request body" },
        execution_time_ms: { bsonType: "number", description: "Execution time in milliseconds" },
        result_count: { bsonType: "int", description: "Number of results returned" },
        status_code: { bsonType: "int", description: "HTTP status code" },
        timestamp: { bsonType: "date", description: "Query timestamp" },
        ip_address: { bsonType: "string" }
      }
    }
  }
});

// Create indexes for performance
db.audit_logs.createIndex({ timestamp: -1 });
db.audit_logs.createIndex({ table: 1, timestamp: -1 });
db.audit_logs.createIndex({ operation: 1, timestamp: -1 });
db.audit_logs.createIndex({ site: 1, timestamp: -1 });

db.query_history.createIndex({ timestamp: -1 });
db.query_history.createIndex({ endpoint: 1, timestamp: -1 });
db.query_history.createIndex({ method: 1, timestamp: -1 });

print('✅ MongoDB initialization completed!');
print('📊 Collections created: audit_logs, query_history');
print('🔍 Indexes created for optimal query performance');
