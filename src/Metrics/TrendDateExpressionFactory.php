<?php

namespace Laravel\Nova\Metrics;

use InvalidArgumentException;
use Illuminate\Database\Eloquent\Builder;

class TrendDateExpressionFactory
{
    /**
     * Create a new trend expression instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * @param  string  $unit
     * @param  string  $timezone
     * @return \Laravel\Nova\Metrics\TrendDateExpression
     */
    public static function make(Builder $query, $column, $unit, $timezone)
    {
        switch ($query->getConnection()->getDriverName()) {
            case 'sqlite':
                return new SqliteTrendDateExpression($query, $column, $unit, $timezone);
            case 'mysql':
            case 'mariadb':
                return new MySqlTrendDateExpression($query, $column, $unit, $timezone);
            case 'pgsql':
                return new PostgresTrendDateExpression($query, $column, $unit, $timezone);
            default:
                throw new InvalidArgumentException('Trend metric helpers are not supported for this database.');
        }
    }
}
