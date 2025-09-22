UPDATE contractors 
SET commission_type = 'tiered', 
    commission_tiers = '[
        {"min": 0, "max": 100000, "rate": 1},
        {"min": 100000, "max": 250000, "rate": 2},
        {"min": 250000, "max": 500000, "rate": 3},
        {"min": 500000, "max": 1000000, "rate": 4},
        {"min": 1000000, "max": null, "rate": 5}
    ]'
WHERE unique_id = 'CO5731295844';
