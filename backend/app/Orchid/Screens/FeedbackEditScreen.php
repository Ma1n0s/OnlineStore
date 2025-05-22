<?php

namespace App\Orchid\Screens;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Relation;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;

class FeedbackEditScreen extends Screen
{
    public $feedback;

    public function name(): ?string
    {
        return $this->feedback->exists ? 'Редактирование обращения' : 'Создание обращения';
    }

    public function description(): ?string
    {
        return "Обращение от пользователя";
    }

    public function query(Feedback $feedback): array
    {
        return [
            'feedback' => $feedback,
        ];
    }

    public function commandBar(): array
    {
        return [
            Button::make('Сохранить')
                ->icon('check')
                ->method('save'),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Relation::make('feedback.user_id')
                    ->title('Пользователь')
                    ->fromModel(User::class, 'name')
                    ->disabled(),
                
                Input::make('feedback.subject')
                    ->title('Тема')
                    ->required()
                    ->disabled(),
                
                TextArea::make('feedback.message')
                    ->title('Сообщение')
                    ->rows(5)
                    ->required()
                    ->disabled(),
                
                Input::make('feedback.rating')
                    ->title('Оценка')
                    ->type('number')
                    ->disabled(),
                
                Select::make('feedback.status')
                    ->title('Статус')
                    ->options(Feedback::statuses())
                    ->required(),
                
                TextArea::make('feedback.admin_notes')
                    ->title('Заметки администратора')
                    ->rows(3),
            ]),
        ];
    }

    public function save(Feedback $feedback, Request $request)
    {
        $feedback->fill($request->get('feedback'))->save();
        
        Alert::info('Обращение успешно обновлено.');
        
        return redirect()->route('platform.feedback.list');
    }
}