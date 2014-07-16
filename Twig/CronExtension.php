<?php

namespace SymfonyContrib\Bundle\CronBundle\Twig;

/**
 *
 */
class CronExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('cron_date_diff', [$this, 'dateDiffFilter'], ['is_safe' => ['html']]),
        );
    }

    public function dateDiffFilter($date, $compare = 'now', $format = '')
    {
        $date    = $date    instanceof \DateTime ? $date    : new \DateTime($date);
        $compare = $compare instanceof \DateTime ? $compare : new \DateTime($compare);

        $interval = $date->diff($compare);

        $diff = '';
        if ($interval->invert === 0) {
            $diff = '<span class="label label-danger">OVER DUE</span>';
        } else {
            if ($interval->y !== 0) {
                $diff = $interval->format('%y years %m months');
            } else if ($interval->m > 0) {
                $diff = $interval->format('%m months %d days');
            } else if ($interval->d > 0) {
                $diff = $interval->format('%d days %h hours');
            } else if ($interval->h > 0) {
                $diff = $interval->format('%H:%I');
            } else if ($interval->i > 0) {
                $diff = $interval->format('%i minutes');
            } else if ($interval->s > 0) {
                $diff = $interval->format('%s seconds');
            }
        }

        return $diff;
    }

    public function getName()
    {
        return 'cron_extension';
    }
}
