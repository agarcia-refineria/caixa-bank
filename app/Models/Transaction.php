<?php

namespace App\Models;

use Auth;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * 
 *
 * @property int $id
 * @property string|null $entryReference
 * @property string|null $checkId
 * @property Carbon|null $bookingDate
 * @property Carbon|null $valueDate
 * @property string|null $transactionAmount_amount
 * @property string|null $transactionAmount_currency
 * @property string|null $remittanceInformationUnstructured
 * @property string|null $bankTransactionCode
 * @property string|null $proprietaryBankTransactionCode
 * @property string|null $internalTransactionId
 * @property string|null $debtorName
 * @property string|null $debtorAccount
 * @property string $account_id
 * @property-read Account $account
 * @property-read mixed $code
 * @property-read string $debtor_name_format
 * @method static Builder|Transaction newModelQuery()
 * @method static Builder|Transaction newQuery()
 * @method static Builder|Transaction orderDate()
 * @method static Builder|Transaction query()
 * @method static Builder|Transaction whereAccountId($value)
 * @method static Builder|Transaction whereBankTransactionCode($value)
 * @method static Builder|Transaction whereBookingDate($value)
 * @method static Builder|Transaction whereCheckId($value)
 * @method static Builder|Transaction whereDebtorAccount($value)
 * @method static Builder|Transaction whereDebtorName($value)
 * @method static Builder|Transaction whereEntryReference($value)
 * @method static Builder|Transaction whereId($value)
 * @method static Builder|Transaction whereInternalTransactionId($value)
 * @method static Builder|Transaction whereProprietaryBankTransactionCode($value)
 * @method static Builder|Transaction whereRemittanceInformationUnstructured($value)
 * @method static Builder|Transaction whereTransactionAmountAmount($value)
 * @method static Builder|Transaction whereTransactionAmountCurrency($value)
 * @method static Builder|Transaction whereValueDate($value)
 * @property int|null $category_id
 * @property-read Category|null $category
 * @property-read string $remittance_information_unstructured_format
 * @method static Builder|Transaction whereCategoryId($value)
 * @mixin Eloquent
 */
class Transaction extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $casts = [
        'bookingDate' => 'datetime',
        'valueDate' => 'datetime',
    ];

    protected $fillable = [
        'id',
        'entryReference',
        'checkId',
        'bookingDate',
        'valueDate',
        'transactionAmount_amount',
        'transactionAmount_currency',
        'remittanceInformationUnstructured',
        'bankTransactionCode',
        'proprietaryBankTransactionCode',
        'internalTransactionId',
        'debtorName',
        'debtorAccount',
        'account_id',
        'category_id',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    /**
     * Scope a query to only include transactions of a given account.
     *
     * @noinspection PhpUnused
     * @param $query
     * @return mixed
     */
    public function scopeOrderDate($query): mixed
    {
        return $query->orderBy('bookingDate', 'desc');
    }

    /**
     * Get the transaction code.
     *
     * @noinspection PhpUnused
     * @return mixed
     */
    public function getCodeAttribute(): mixed
    {
        return $this->attributes['id'];
    }

    /**
     * Get the debtor name in a formatted way.
     *
     * @noinspection PhpUnused
     * @return string
     */
    public function getDebtorNameFormatAttribute(): string
    {
        return $this->attributes['debtorName'] ? str_replace(';', ' ', $this->attributes['debtorName']) : '--';
    }

    /**
     * Accessor for the `remittance_information_unstructured_format` attribute.
     *
     * This accessor processes the `remittanceInformationUnstructured` attribute,
     * handling its JSON-decoded value. If the decoded value is an array, it is
     * converted to a string with elements separated by commas. If it is a string,
     * any double quotes are removed. If neither, it defaults to '--'.
     *
     * @noinspection PhpUnused
     * @return string The formatted remittance information or a fallback value.
     */
    public function getRemittanceInformationUnstructuredFormatAttribute(): string
    {
        $value = json_decode($this->attributes['remittanceInformationUnstructured']) ?? '--';

        // If the value is an array, convert it to a string
        if (is_array($value)) {
            return implode(', ', $value);
        }
        // If the value is a string, return it as is
        if (is_string($value)) {
            // Remove " from the value if it exists
            return str_replace('"', '', $value);
        }

        return '--';
    }

    /**
     * Sets a formatted representation of the remittance information unstructured attribute.
     *
     * The method removes specific characters ('[', ']', and '"') from the input attribute
     * and wraps the processed value in properly formatted quotation marks and square brackets.
     *
     * @param mixed $attribute The original attribute to be formatted.
     * @return string The formatted attribute.
     */
    public static function setRemittanceInformationUnstructuredFormat(mixed $attribute): string
    {
        // remove [ and ] and " from the attribute
        $attribute = str_replace(['[', ']', '"'], '', $attribute);

        return "[\"" . $attribute ."\"]";
    }

    /**
     * Retrieve the category ID based on the given value and the authenticated user's associated categories and filters.
     *
     * This method iterates over the categories and their filters of the currently authenticated user
     *  to determine if the provided value matches any filter condition. It supports multiple
     * types of filters such as 'exact', 'contains', 'starts_with', and 'ends_with'.
     *
     * If a match is found, it returns the corresponding category ID. If no match is found or no value
     * is provided and the user has no associated categories, it returns null.
     *
     * @param string|null $value The input value to compare against the filters.
     * @return int|null The matched category ID or null if no match is found.
     */
    public static function getCategoryId(string $value = null): ?int
    {
        $user = Auth::user();
        $categories = $user->categories;

        if ($categories->isEmpty() && is_null($value)) {
            return null;
        }

        foreach ($categories as $category) {
            $filters = $category
                ->filters()
                ->where('enabled', true)
                ->get();

            foreach ($filters as $filter) {
                switch ($filter->type) {
                    case 'exact':
                        // Check if the value matches exactly
                        // Remove [] from the value if it exists
                        $value = str_replace(['[', ']'], '', $value);

                        // Remove " from the value if it exists
                        $value = str_replace('"', '', $value);

                        // Compare the value with the filter value
                        if (strcasecmp($value, $filter->value) === 0) {
                            return $category->id;
                        }
                        break;
                    case 'contains':
                        // Check if the value contains the filter value
                        if (stripos($value, $filter->value) !== false) {
                            return $category->id;
                        }
                        break;
                    case 'starts_with':
                        // Check if the value starts with the filter value
                        if (str_starts_with($value, $filter->value)) {
                            return $category->id;
                        }
                        break;
                    case 'ends_with':
                        // Check if the value ends with the filter value
                        if (str_ends_with($value, $filter->value)) {
                            return $category->id;
                        }
                        break;
                }
            }
        }

        return null;
    }

    public static function getExampleModel(): Transaction
    {
        return new self([
            'id' => 5,
            'entryReference' => '1234567890',
            'checkId' => '1234567890',
            'bookingDate' => now(),
            'valueDate' => now(),
            'transactionAmount_amount' => 100.00,
            'transactionAmount_currency' => 'EUR',
            'remittanceInformationUnstructured' => 'Payment for services',
            'bankTransactionCode' => '1234',
            'proprietaryBankTransactionCode' => '5678',
            'internalTransactionId' => 'abcd1234',
            'debtorName' => 'John Doe',
            'debtorAccount' => 'DE89370400440532013000',
        ]);
    }
}
