<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\CompanyInfo;
use App\Events\CompanyInfoEventSent;

class CompanyInfoForm extends Component
{
    use WithFileUploads;

    // Model key & uploads
    public ?int $company_id = null;
    public $image; // Livewire temp file

    // Text fields
    public string $name_en = '';
    public string $name_ar = '';
    public string $email = '';
    public string $phone = '';
    public string $address_en = '';
    public string $address_ar = '';
    public string $location = '';
    public string $about_us_en = '';
    public string $about_us_ar = '';
    public string $mission_en = '';
    public string $mission_ar = '';
    public string $vision_en = '';
    public string $vision_ar = '';

    // Color fields (safe defaults)
    public string $main_color = '#ffffff';
    public string $sub_color = '#ffffff';
    public string $text_color = '#000000';
    public string $button_color = '#4f46e5';
    public string $button_text_color = '#ffffff';
    public string $icon_color = '#111827';
    public string $text_field_color = '#ffffff';
    public string $card_color = '#ffffff';
    public string $label_color = '#111827';
    public string $hint_color = '#6b7280';

    protected $listeners = [
        // From JS (Echo -> Livewire.dispatch('refreshFromBroadcast', payload))
        'refreshFromBroadcast' => 'hydrateFromPayload',
    ];

    public function mount(?CompanyInfo $company = null): void
    {
        if ($company && $company->exists) {
            $this->fillFromModel($company);
        } else {
            if ($last = CompanyInfo::query()->latest('id')->first()) {
                $this->fillFromModel($last);
            }
        }
    }

    /** Safely hydrate properties from model without clobbering defaults for colors */
    private function fillFromModel(CompanyInfo $company): void
    {
        $this->company_id = $company->id;

        // plain text
        foreach ([
            'name_en','name_ar','email','phone','address_en','address_ar','location',
            'about_us_en','about_us_ar','mission_en','mission_ar','vision_en','vision_ar',
        ] as $f) {
            $val = data_get($company, $f);
            if (!is_null($val) && $val !== '') {
                $this->$f = (string) $val;
            }
        }

        // colors (keep defaults if invalid/empty)
        foreach ([
            'main_color','sub_color','text_color','button_color','button_text_color','icon_color',
            'text_field_color','card_color','label_color','hint_color',
        ] as $f) {
            $this->$f = $this->normalizeHex(data_get($company, $f), $this->$f);
        }
    }

    /** Normalize hex: accept #rgb or #rrggbb, else fallback */
    private function normalizeHex($v, string $fallback): string
    {
        if (is_string($v)) {
            $v = trim($v);
            if (preg_match('/^#([0-9a-fA-F]{6})$/', $v)) return strtolower($v);
            if (preg_match('/^#([0-9a-fA-F]{3})$/', $v)) {
                return strtolower('#' . $v[1].$v[1].$v[2].$v[2].$v[3].$v[3]);
            }
        }
        return $fallback;
    }

    public function rules(): array
    {
        $hex = ['required','regex:/^#([0-9a-fA-F]{6})$/']; // enforce #rrggbb
        return [
            'name_en' => ['required','string','max:255'],
            'name_ar' => ['required','string','max:255'],
            'email'   => ['nullable','email','max:255'],
            'phone'   => ['nullable','string','max:50'],
            'address_en' => ['nullable','string','max:500'],
            'address_ar' => ['nullable','string','max:500'],
            'location'   => ['nullable','string','max:255'],
            'about_us_en'=> ['nullable','string','max:2000'],
            'about_us_ar'=> ['nullable','string','max:2000'],
            'mission_en' => ['nullable','string','max:1000'],
            'mission_ar' => ['nullable','string','max:1000'],
            'vision_en'  => ['nullable','string','max:1000'],
            'vision_ar'  => ['nullable','string','max:1000'],

            // Colors
            'main_color'        => $hex,
            'sub_color'         => $hex,
            'text_color'        => $hex,
            'button_color'      => $hex,
            'button_text_color' => $hex,
            'icon_color'        => $hex,
            'text_field_color'  => $hex,
            'card_color'        => $hex,
            'label_color'       => $hex,
            'hint_color'        => $hex,

            // Image optional (4MB)
            'image' => ['nullable','image','max:4096'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $data = $this->only([
            'name_en','name_ar','email','phone','address_en','address_ar','location',
            'about_us_en','about_us_ar','mission_en','mission_ar','vision_en','vision_ar',
            'main_color','sub_color','text_color','button_color','button_text_color','icon_color',
            'text_field_color','card_color','label_color','hint_color',
        ]);

        $company = $this->company_id
            ? CompanyInfo::findOrFail($this->company_id)
            : new CompanyInfo();

        $company->fill($data);

        if ($this->image) {
            $path = $this->image->store('company', 'public');
            $company->image = $path; // ensure `image` column exists
        }

        $company->save();
        $this->company_id = $company->id;

        // Broadcast to public channel so other clients refresh (not sender)
        broadcast(new CompanyInfoEventSent($company))->toOthers();

        // Livewire v3 browser event (your layout listens and shows a toast)
        $this->dispatch('toast', type: 'success', message: __('Saved successfully'));
    }

    /** Called when Echo receives the broadcast and we re-emit into Livewire */
    public function hydrateFromPayload(array $payload): void
    {
        $c = $payload['company'] ?? $payload;

        // text
        foreach ([
            'name_en','name_ar','email','phone','address_en','address_ar','location',
            'about_us_en','about_us_ar','mission_en','mission_ar','vision_en','vision_ar',
        ] as $k) {
            if (array_key_exists($k, $c) && is_scalar($c[$k]) && $c[$k] !== '') {
                $this->$k = (string) $c[$k];
            }
        }

        // colors
        foreach ([
            'main_color','sub_color','text_color','button_color','button_text_color','icon_color',
            'text_field_color','card_color','label_color','hint_color',
        ] as $k) {
            if (array_key_exists($k, $c)) {
                $this->$k = $this->normalizeHex((string)$c[$k], $this->$k);
            }
        }
    }

    public function render()
    {
        $company = $this->company_id ? CompanyInfo::find($this->company_id) : null;
        return view('livewire.company-info-form', compact('company'));
    }
}
