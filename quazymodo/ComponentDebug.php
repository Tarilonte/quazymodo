<?php

namespace Quazymodo;

use Tracy\Debugger;
use Tracy\IBarPanel;

class ComponentDebug extends BaseComponent
{
    private static $components = [];
    private static $panelAdded = false;
    private $startTime;

    public function __construct($componentName, $inserts = [], $componentType)
    {
        $this->startTime = microtime(true);
        parent::__construct($componentName, $inserts, $componentType);
        $executionTime = microtime(true) - $this->startTime;
        self::$components[] = [
            'name' => $this->componentName,
            'time' => $executionTime,
            'slots' => $this->slots
        ];
        self::addPanel();
    }

    public static function getComponents()
    {
        return self::$components;
    }

    private static function addPanel()
    {
        if (!self::$panelAdded) {
            Debugger::getBar()->addPanel(new ComponentPanel());
            self::$panelAdded = true;
        }
    }
}

class ComponentPanel implements IBarPanel
{
    public function getTab(): string
    {
        $components = ComponentDebug::getComponents();
        $componentCount = count($components);
        return "<svg viewBox='0 0 24 24' fill='none' stroke='dodgerblue' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
                <path stroke='none' d='M0 0h24v24H0z' fill='none' />
                <path d='M5 4h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1' />
                <path d='M5 16h4a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-2a1 1 0 0 1 1 -1' />
                <path d='M15 12h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1' />
                <path d='M15 4h4a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-2a1 1 0 0 1 1 -1' />
              </svg>  $componentCount";
    }

    public function getPanel(): string
    {
        $components = ComponentDebug::getComponents();
        $totalTime = array_sum(array_column($components, 'time'));
        $componentCount = count($components);

        // Agregando os dados dos componentes
        $componentData = [];
        foreach ($components as $component) {
            $name = $component['name'];
            if (!isset($componentData[$name])) {
                $componentData[$name] = [
                    'name' => $name,
                    'instances' => 0,
                    'time' => 0,
                    'slots' => $component['slots']
                ];
            }
            $componentData[$name]['instances']++;
            $componentData[$name]['time'] += $component['time'];
        }

        ob_start();
        echo '<h1>Components</h1>';
        echo '<div>Total components: ' . $componentCount . '</div>';
        echo '<div>Total execution time: ' . number_format($totalTime * 1000, 2) . ' ms</div>';
        echo '<div class="tracy-inner-container">';
        echo '<table class="tracy-sortable">';
        echo '<tr><th>Component</th><th>Instances</th><th>Time (ms)</th><th>Slots</th></tr>';
        foreach ($componentData as $data) {
            echo '<tr>
                <td style="white-space:nowrap; font-weight:bold">' . $data['name'] . '</td>
                <td>' . $data['instances'] . '</td>
                <td>' . number_format($data['time'] * 1000, 2) . '</td>
                <td>
                    <span style="background:#fff6; padding:1px 6px; margin:2px; display:inline-block; border:1px solid #0004;">' 
                        . implode('</span><span style="background:#fff6; padding:1px 6px; margin:2px; display:inline-block; border:1px solid #0004;">', $data['slots']). 
                    '</span>
                </td>
            </tr>';
        }
        echo '</table>';
        echo '</div>';
        return ob_get_clean();
    }
}
