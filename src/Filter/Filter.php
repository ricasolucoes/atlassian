<?php
/**
 * @file
 * Contains the Filter class.
 */

namespace Atlassian\Filter;

class Filter
{
    /**
     * @var string[][]
     *
     * @psalm-var array{resolution: array{0: string}}
     */
    private array $bugWontFix;

    /**
     * @var string[][]
     *
     * @psalm-var array{resolution: array{0: string}}
     */
    private array $cannotReproduce;

    /**
     * @var string[][]
     *
     * @psalm-var array{status: array{0: string, 1: string, 2: string}}
     */
    private array $closed;

    /**
     * @var string[][]
     *
     * @psalm-var array{priority: array{0: string}}
     */
    private array $critical;

    /**
     * @var string[][]
     *
     * @psalm-var array{issuetype: array{0: string, 1: string, 2: string}}
     */
    private array $defects;

    /**
     * @var string[][]
     *
     * @psalm-var array{resolution: array{0: string}}
     */
    private array $duplicate;

    /**
     * @var string[][]
     *
     * @psalm-var array{priority: array{0: string}}
     */
    private array $high;

    /**
     * @var string[][]
     *
     * @psalm-var array{resolution: array{0: string}}
     */
    private array $moreInfoNeeded;

    /**
     * @var string[][]
     *
     * @psalm-var array{resolution: array{0: string}}
     */
    private array $notBugWontFix;

    /**
     * @var string[][]
     *
     * @psalm-var array{status: array{0: string, 1: string, 2: string, 3: string, 4: string, 5: string, 6: string, 7: string, 8: string, 9: string, 10: string, 11: string}}
     */
    private array $open;

    /**
     * @var string[][]
     *
     * @psalm-var array{priority: array{0: string, 1: string, 2: string}}
     */
    private array $priority;

    /**
     * @var string[][]
     *
     * @psalm-var array{priority: array{0: string}}
     */
    private array $toBeDetermined;

    /**
     * @var string[][]
     *
     * @psalm-var array{priority: array{0: string}}
     */
    private array $urgent;

    /**
     * @var string[][][]
     *
     * @psalm-var array{bwf: array{resolution: array{0: string}}, cannot_reproduce: array{resolution: array{0: string}}, closed: array{status: array{0: string, 1: string, 2: string}}, critical: array{priority: array{0: string}}, defects: array{issuetype: array{0: string, 1: string, 2: string}}, duplicate: array{resolution: array{0: string}}, high: array{priority: array{0: string}}, min: array{resolution: array{0: string}}, nbwf: array{resolution: array{0: string}}, open: array{status: array{0: string, 1: string, 2: string, 3: string, 4: string, 5: string, 6: string, 7: string, 8: string, 9: string, 10: string, 11: string}}, priority: array{priority: array{0: string, 1: string, 2: string}}, urgent: array{priority: array{0: string}}, tbd: array{priority: array{0: string}}}
     */
    protected array $filters;

    public function __construct()
    {
        // JQL Filters.
        $this->defects = array(
        'issuetype' => array(
        'Bug',
        'Risk',
        'Security mitigation',
        ),
        );

        // Missing: 'Needs Justification'.
        $this->open = array(
        'status' => array(
        'Open',
        'Testing',
        'Kanban To Do',
        'Good Idea',
        'In Progress',
        'Reopened',
        'In Code Review',
        'In Design',
        'In Roadmap',
        'Ready For Dev',
        'Design Review',
        'Ready To Release',
        ),
        );

        $this->closed = array(
        'status' => array(
        'Closed',
        'Rejected',
        'Done',
        ),
        );

        $this->cannotReproduce = array(
        'resolution' => array('Cannot Reproduce'),
        );

        $this->duplicate = array(
        'resolution' => array('Duplicate'),
        );

        $this->bugWontFix = array(
        'resolution' => array("Bug, but won't fix"),
        );

        $this->notBugWontFix = array(
        'resolution' => array("Not a bug, won't fix"),
        );

        $this->moreInfoNeeded = array(
        'resolution' => array('More Info Needed'),
        );

        $this->critical = array(
        'priority' => array('Critical'),
        );

        $this->high = array(
        'priority' => array('High'),
        );

        $this->urgent = array(
        'priority' => array('Urgent'),
        );

        $this->priority = array(
        'priority' => array('Critical', 'High', 'Urgent'),
        );

        $this->toBeDetermined = array(
        'priority' => array('TBD'),
        );

        // JQL filters.
        $this->filters = array(
        'bwf' => $this->bugWontFix,
        'cannot_reproduce' => $this->cannotReproduce,
        'closed' => $this->closed,
        'critical' => $this->critical,
        'defects' => $this->defects,
        'duplicate' => $this->duplicate,
        'high' => $this->high,
        'min' => $this->moreInfoNeeded,
        'nbwf' => $this->notBugWontFix,
        'open' => $this->open,
        'priority' => $this->priority,
        'urgent' => $this->urgent,
        'tbd' => $this->toBeDetermined,
        );
    }

    /**
     *
     * @return Filter
     */
    public function getFilters()
    {
        return $this->filters;
    }

}
