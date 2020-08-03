SELECT
	worker.first_name AS worker_first_name,
    worker.last_name AS worker_last_name,
    GROUP_CONCAT(child.name SEPARATOR ',') AS child_name,
    (SELECT model FROM car WHERE car.user_id = worker.id) AS car_model
FROM
	worker, child, car
WHERE
	worker.id IN (SELECT user_id
        FROM car
        WHERE model IS NULL OR model != '')
	AND worker.id = child.user_id
    AND worker.id = car.user_id
GROUP BY
	worker.id