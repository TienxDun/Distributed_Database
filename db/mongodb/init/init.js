// Switch to database
db = db.getSiblingDB('huflit_logs');

// Create collections without validation for development
db.createCollection('audit_logs');
db.createCollection('query_history');

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
