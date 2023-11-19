<?php

namespace Tests;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Closure;
use Illuminate\Testing\Fluent\AssertableJson as BaseAssertableJson;
use PHPUnit\Framework\Assert as PHPUnit;

class AssertableJson extends BaseAssertableJson
{
	/**
     * Validate the value of the given property as a UUID.
     *
     * @param string $key The property key.
     * @param bool $nullable Indicates whether the value can be null.
     * @return static
     * @throws PHPUnit\Framework\ExpectationFailedException
     */
	public function whereTypeUuid(string $key, bool $nullable = false): static
	{
		$this->has($key);

		$value = $this->prop($key);

		if ($nullable) {
			if($value == null) {
				$this->interactsWith($key);

				return $this;
			}
		}

		PHPUnit::assertTrue(
			preg_match('/^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$/', $value) === 1,
			sprintf('Property [%s] is not of expected type uuid. Value [%s]', $this->dotPath($key), $value)
		);

		return $this;
	}

    /**
     * Validate the value of the given property as an enum.
     *
     * @param string $key The property key.
     * @param array $enum The enum.
     * @param bool $nullable Indicates whether the value can be null.
     * @return static
     * @throws PHPUnit\Framework\ExpectationFailedException
     */
    public function whereTypeEnum(string $key, array $enum, bool $nullable = false): static
    {
        $this->has($key);

        $value = $this->prop($key);

        if ($nullable) {
            if($value == null) {
                $this->interactsWith($key);

                return $this;
            }
        }

        PHPUnit::assertTrue(
            in_array($value, $enum),
            sprintf('Property [%s] is not of expected type enum. Value [%s]', $this->dotPath($key), $value)
        );

        return $this;
    }

    /**
     * Validate the value of the given property as a class name.
     *
     * @param string $key The property key.
     * @param bool $nullable Indicates whether the value can be null.
     * @return static
     * @throws PHPUnit\Framework\ExpectationFailedException
     */
	public function whereTypeClass(string $key, bool $nullable = false): static
	{
		$this->has($key);

		$value = $this->prop($key);

		if ($nullable) {
			if($value == null) {
				$this->interactsWith($key);

				return $this;
			}
		}

        if (env('APP_DEBUG', false)) {
            PHPUnit::assertTrue(
                class_exists($value),
                sprintf('Property [%s] is not of expected type class.', $this->dotPath($key))
            );
        }

		return $this;
	}

    /**
     * Validate the value of the given property as a timestamp.
     *
     * @param string $key The property key.
     * @param bool $nullable Indicates whether the value can be null.
     * @return static
     * @throws PHPUnit\Framework\ExpectationFailedException
     */
	public function whereTypeTimestamp(string $key, bool $nullable = false): static
	{
		$this->has($key);

		$value = $this->prop($key);

		if ($nullable) {
			if($value == null) {
				$this->interactsWith($key);

				return $this;
			}
		}

		$isCorrectFormat = true;

		try {
			Carbon::parse($value);
		} catch (InvalidFormatException $e) {
			$isCorrectFormat = false;
		}

		PHPUnit::assertTrue(
			$isCorrectFormat,
			sprintf('Property [%s] is not of expected type timestamp.', $this->dotPath($key))
		);

		return $this;
	}

    /**
     * For each property, call the given callback. If the property is nullable, the callback will not be called.
     * The callback will receive the property key as the first argument.
     * The callback should return an instance of AssertableJson.
     *
     * @param Closure $callback The callback.
     * @return static
     */
    public function eachNullable(Closure $callback): static
    {
        $props = $this->prop();

        if (empty($props)) {
            return $this;
        }

        foreach (array_keys($props) as $key) {
            $this->interactsWith($key);

            $this->scope($key, $callback);
        }

        return $this;
    }

    /**
     * Works as has() but for nullable properties.
     *
     * @param string $key The property key.
     * @param Closure $callback The callback.
     * @return static
     */
    public function hasNullable(string $key, Closure $callback): static
    {
        if ($this->prop($key) != null) {
            $this->interactsWith($key);

            $this->scope($key, $callback);
        }

        return $this;
    }
}
