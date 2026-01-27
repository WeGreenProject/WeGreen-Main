-- Migration: Add quantidade column to devolucoes table
-- Date: 2026-01-26
-- Purpose: Support partial product returns (product-level returns)

-- Add quantidade column to track how many units of the product are being returned
ALTER TABLE devolucoes
ADD COLUMN quantidade INT NOT NULL DEFAULT 1 AFTER produto_id;

-- Update existing records to have quantidade = 1 (full return)
UPDATE devolucoes SET quantidade = 1 WHERE quantidade IS NULL OR quantidade = 0;

-- Add comment to the column
ALTER TABLE devolucoes
MODIFY COLUMN quantidade INT NOT NULL DEFAULT 1 COMMENT 'Quantidade do produto sendo devolvida';
