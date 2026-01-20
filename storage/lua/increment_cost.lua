-- KEYS[1] = totalCostKey
-- ARGV[1] = hitCost
-- ARGV[2] = maxTotal
local current = redis.call("GET", KEYS[1])
if not current then
    current = 0
end
current = tonumber(current)
local hitCost = tonumber(ARGV[1])
local maxTotal = tonumber(ARGV[2])

if current + hitCost > maxTotal then
    return -1
else
    local newTotal = redis.call("INCRBY", KEYS[1], hitCost)
    -- 设置过期时间1天（秒）
    redis.call("EXPIRE", KEYS[1], 86400)
    return newTotal
end
