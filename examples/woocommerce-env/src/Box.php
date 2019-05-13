<?php

namespace Examples\WoocommerceEnv;

class Box
{

    const INCH = 'in';

    const METER = 'm';

    const CENTIMETER = 'cm';

    const MILLIMETER = 'mm';

    const GRAM = 'g';

    const KG = 'kg';

    const LB = 'lbs';

    const OZ = 'oz';

    /**
     * @var string
     */
    protected $type;
    /**
     * @var array
     */
    protected $dimensions;

    /**
     * @var string
     */
    protected $unit;
    /**
     * @var string
     */
    protected $original_unit;

    /**
     * @var array
     */
    protected $original_dimensions;

    public function __construct($type, array $dimensions, $length_unit = 'in', $weight_unit = 'lbs')
    {
        if (! \is_string($type)) {
            throw new \InvalidArgumentException('Box type should be a string');
        }

        if (\count(array_filter($dimensions, 'is_numeric')) !== 4) {
            throw new \InvalidArgumentException('Box dimensions should be 4 integers: max width, max length, max height, max weight.');
        }

        if (array_map('abs', $dimensions) !== $dimensions) {
            throw new \InvalidArgumentException('Box dimensions should all be positive integers.');
        }

        if (! \in_array($length_unit, static::supported_length_units(), true)) {
            throw new \InvalidArgumentException(
                "Box unit {$length_unit} is not supported, supported units are " . implode(
                    ', ',
                    static::supported_length_units()
                )
            );
        }
        if (! \in_array($weight_unit, static::supported_weight_units(), true)) {
            throw new \InvalidArgumentException(
                "Box unit {$weight_unit} is not supported, supported units are " . implode(
                    ', ',
                    static::supported_weight_units()
                )
            );
        }

        $this->type = $type;
        $this->original_unit = $length_unit;
        $this->original_dimensions = $dimensions;
        $this->unit = static::INCH;
        $this->dimensions = [
            wc_get_dimension($dimensions[0], $length_unit, static::INCH),
            wc_get_dimension($dimensions[1], $length_unit, static::INCH),
            wc_get_dimension($dimensions[2], $length_unit, static::INCH),
            wc_get_weight($dimensions[3], $weight_unit, static::LB),
        ];
    }

    protected static function supported_length_units()
    {
        return [ static::INCH, static::METER, static::CENTIMETER, static::MILLIMETER ];
    }

    protected static function supported_weight_units()
    {
        return [ static::GRAM, static::KG, static::LB, static::OZ ];
    }

    public function unit()
    {
        return $this->unit;
    }

    public function dimensions()
    {
        return $this->dimensions;
    }

    public function original_unit()
    {
        return $this->original_unit;
    }

    public function original_dimensions()
    {
        return $this->original_dimensions;
    }

    public function type()
    {
        return $this->type;
    }
}
