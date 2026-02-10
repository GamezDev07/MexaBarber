#!/bin/bash
# Script: Exportar datos de MySQL a formato SQL compatible con PostgreSQL

echo "📦 Exportando datos MySQL..."
echo ""

# Exportar estructura
mysqldump -u root -proot -d appsalon > mysql_schema.sql

# Exportar datos
mysqldump -u root -proot --no-create-info appsalon > mysql_data.sql

echo "✅ Exportación completada"
echo ""
echo "Archivos generados:"
echo "  - mysql_schema.sql (estructura)"
echo "  - mysql_data.sql (datos)"
echo ""
echo "Próximo paso: Convertir a PostgreSQL usando migrate.guru o pgloader"
