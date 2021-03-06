<?php

declare(strict_types = 1);
namespace Gaara\Core\Model\QueryBuilder;

use Gaara\Core\Model\QueryBuilder;

trait Support {

	/**
	 * 生成sql
	 * @param array $pars 参数绑定, 在此处, 仅作记录sql作用
	 * @return string sql
	 */
	public function toSql(array $pars = []): string {
		$sql      = '';
		$remember = true;
		switch ($this->sqlType) {
			case 'select':
				$sql = 'select ' . $this->dealSelect() . $this->dealFromSelect();
				break;
			case 'update':
				$sql = 'update ' . $this->dealFrom() . ' set' . $this->dealData();
				break;
			case 'insert':
				$sql = 'insert into ' . $this->dealFrom() . ' set' . $this->dealData();
				break;
			case 'replace':
				$sql = 'replace into ' . $this->dealFrom() . ' set' . $this->dealData();
				break;
			case 'delete':
				$sql = 'delete from ' . $this->dealFrom();
				break;
			default :
				$remember = false;
				break;
		}
		$sql .= $this->dealJoin() . $this->dealWhere() . $this->dealGroup() . $this->dealHaving() . $this->dealOrder() .
		        $this->dealLimit() . $this->dealLock();
		if (!empty($this->union)) {
			$sql = $this->bracketFormat($sql);
			foreach ($this->union as $type => $clauseArray) {
				foreach ($clauseArray as $clause)
					$sql .= $type . $this->bracketFormat($clause);
			}
		}
		if ($remember)
			$this->rememberSql($sql, $pars);
		return $sql;
	}

	/**
	 * 返回select部分
	 * @return string
	 */
	protected function dealSelect(): string {
		if (is_null($this->select)) {
			return '*';
		}
		else {
			return $this->select;
		}
	}

	/**
	 * 返回from部分 select 专用
	 * @return string
	 */
	protected function dealFromSelect(): string {
		if ($this->noFrom === true)
			return '';
		if (is_null($this->from)) {
			return ' from `' . $this->table . '`';
		}
		else {
			return ' from ' . $this->from;
		}
	}

	/**
	 * 返回from部分
	 * @return string
	 */
	protected function dealFrom(): string {
		if (is_null($this->from)) {
			return '`' . $this->table . '`';
		}
		else {
			return $this->from;
		}
	}

	/**
	 * data
	 * @return string
	 */
	protected function dealData(): string {
		if (is_null($this->data)) {
			return '';
		}
		else {
			return ' ' . $this->data;
		}
	}

	/**
	 * join
	 * @return string
	 */
	protected function dealJoin(): string {
		if (is_null($this->join)) {
			return '';
		}
		else
			return ' ' . $this->join;
	}

	/**
	 * 返回where部分
	 * @return string
	 */
	protected function dealWhere(): string {
		if (is_null($this->where)) {
			return '';
		}
		else {
			if (is_null($this->sqlType)) {
				return $this->where;
			}
			else {
				return ' where ' . $this->where;
			}
		}
	}

	/**
	 * group
	 * @return string
	 */
	protected function dealGroup(): string {
		if (is_null($this->group)) {
			return '';
		}
		else {
			return ' group by ' . $this->group;
		}
	}

	/**
	 * having
	 * @return string
	 */
	protected function dealHaving(): string {
		if (is_null($this->having)) {
			return '';
		}
		else {
			return ' having ' . $this->having;
		}
	}

	/**
	 * order
	 * @return string
	 */
	protected function dealOrder(): string {
		if (is_null($this->order)) {
			return '';
		}
		else {
			return ' order by ' . $this->order;
		}
	}

	/**
	 * limit
	 * @return string
	 */
	protected function dealLimit(): string {
		if (is_null($this->limit)) {
			return '';
		}
		else {
			return ' limit ' . $this->limit;
		}
	}

	/**
	 * lock
	 * @return string
	 */
	protected function dealLock(): string {
		if (is_null($this->lock)) {
			return '';
		}
		else {
			return ' ' . $this->lock;
		}
	}

	/**
	 * 值加上括号
	 * @param string $value 字段 eg:1765595948
	 * @return string   eg:(1765595948)
	 */
	protected function bracketFormat(string $value): string {
		return '(' . $value . ')';
	}

	/**
	 * 记录最近次的sql, 完成参数绑定的填充
	 * 重载此方法可用作sql日志
	 * @param string $sql 拼接完成的sql
	 * @param array $pars 参数绑定数组
	 * @return void
	 */
	protected function rememberSql(string $sql, array $pars = []): void {
		foreach ($pars as $k => $v) {
			$pars[$k] = '\'' . $v . '\'';
		}
		$this->lastSql = strtr($sql, $pars);
		$this->model->setLastSql($this->lastSql);
	}

	/**
	 * 获取一个与自己主属性相同的全新实例, 不同于clone
	 * @return QueryBuilder
	 */
	protected function getSelf(): QueryBuilder {
		return new QueryBuilder($this->table, $this->primaryKey, $this->db, $this->model);
	}

	/**
	 * 给与sql片段两端空格
	 * @param string $part sql片段
	 * @return string
	 */
	protected function partFormat(string $part): string {
		return ' ' . trim($part) . ' ';
	}

	/**
	 * 给字段加上反引号
	 * @param string $field 字段 eg: sum(order.amount) as sum_price
	 * @return string eg: sum(`order`.`amount`) as `sum_price`
	 */
	protected function fieldFormat(string $field): string {
		if ($has_as = stristr($field, ' as ')) {
			$as        = substr($has_as, 0, 4);
			$info      = explode($as, $field);
			$alias     = ' as `' . end($info) . '`';
			$maybefunc = reset($info);
		}
		else {
			$alias     = '';
			$maybefunc = $field;    // eg: sum(order.amount)
		}
		if (($a = strstr($maybefunc, '('))) {   // eg: (order.amount)
			$action = str_replace($a, '', $maybefunc);  // eg: sum
			$a      = ltrim($a, '(');
			$a      = rtrim($a, ')');
			if (strstr($a, '.')) {
				$arr  = explode('.', $a);
				$temp = '`' . reset($arr) . '`.`' . end($arr) . '`';
			}
			else
				$temp = '`' . $a . '`';
			$temp = $action . '(' . $temp . ')';
		}
		else {
			if (strpos($maybefunc, '.') === false) {
				$temp = '`' . $maybefunc . '`';
			}
			else {
				$arr  = explode('.', $maybefunc);
				$temp = '`' . reset($arr) . '`.`' . end($arr) . '`';
			}
		}
		return $temp . $alias;
	}

	/**
	 * 将值转化为`绑定参数键`
	 * @param string $value
	 * @return string
	 */
	protected function valueFormat(string $value): string {
		$key                  = ':' . (string)self::$bindingCounter++;
		$this->bindings[$key] = $value;
		return ' ' . $key . ' ';
	}

}
