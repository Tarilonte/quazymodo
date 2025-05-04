<?php

namespace Quazymodo;

use Medoo\Medoo;
use PDOStatement;
use Tracy\IBarPanel;

class MedooDebug extends Medoo
{
    private static $queries = [];
    private static $totalTime = 0;
    private $host;
    private $database;

    public function __construct(array $options, string $host)
    {
        parent::__construct($options);
        $this->host = $host;
        $this->database = $options['database'];
    }

    public function query(string $statement, array $map = []): ?PDOStatement
    {
        $preparedQuery = $this->generate($statement, $map);
        $start = microtime(true);
        $result = parent::query($statement, $map);
        $time = microtime(true) - $start;

        $rowCount = $result ? $result->rowCount() : 0;
        $this->logQuery($preparedQuery, $time, $rowCount);
        return $result;
    }

    public function exec(string $statement, array $map = [], callable $callback = null): ?PDOStatement
    {
        $preparedQuery = $this->generate($statement, $map);
        $start = microtime(true);
        $result = parent::exec($statement, $map, $callback);
        $time = microtime(true) - $start;

        $rowCount = $result ? $result->rowCount() : 0;
        $this->logQuery($preparedQuery, $time, $rowCount);
        return $result;
    }

    private function logQuery(string $query, float $time, int $rowCount): void
    {
        self::$queries[] = [
            'query' => $query,
            'time' => $time,
            'rowCount' => $rowCount,
            'host' => $this->host,
            'database' => $this->database,
        ];
        self::$totalTime += $time;
    }

    public static function getQueries(): array
    {
        return self::$queries;
    }

    public static function getTotalTime(): float
    {
        return self::$totalTime;
    }
}

class MedooPanel implements IBarPanel
{
    public function getTab(): string
    {
        $queryCount = count(MedooDebug::getQueries());
        return "<svg viewBox='0 0 24 24' fill='none' stroke='DodgerBlue' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
        <path stroke='none' d='M0 0h24v24H0z' fill='none' />
        <path d='M12 6m-8 0a8 3 0 1 0 16 0a8 3 0 1 0 -16 0' />
        <path d='M4 6v6a8 3 0 0 0 16 0v-6' />
        <path d='M4 12v6a8 3 0 0 0 16 0v-6' />
        </svg> $queryCount";
    }

    public function getPanel(): string
    {
        $queries = MedooDebug::getQueries();
        $totalTime = MedooDebug::getTotalTime();
        $queryCount = count($queries);

        ob_start();
        echo '<h1>Database Queries</h1>';
        echo '<div>Total time: ' . number_format($totalTime * 1000, 2) . ' ms</div>';
        echo '<div>Query count: ' . $queryCount . '</div>';
        echo '<table>';
        echo '<tr><th>Host</th><th>Database</th><th>Query</th><th>Time (ms)</th><th>Rows returned</th></tr>';
        foreach ($queries as $query) {
            echo '<tr><td>' . $query['host'] . '</td>
                      <td>' . $query['database'] . '</td>
                      <td style="font-family:monospace;">' . $query['query'] . '</td>
                      <td>' . number_format($query['time'] * 1000, 2) . '</td>
                      <td>' . $query['rowCount'] . '</td>
                  </tr>';
        }
        echo '</table>';
        return ob_get_clean();
    }
}
