<?php

namespace App\Orchid\Screens;

use App\Models\User;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Upload;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Illuminate\Support\Facades\Mail;
use App\Mail\FeedbackResponseMail;

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
                
            Button::make('Отправить ответ')
                ->icon('envelope')
                ->method('sendResponse')
                ->canSee($this->feedback->exists && $this->feedback->user),
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
                
                Select::make('feedback.status')
                    ->title('Статус')
                    ->options(Feedback::statuses())
                    ->required(),
                
                TextArea::make('feedback.admin_notes')
                    ->title('Заметки администратора')
                    ->rows(3),
                
                Quill::make('response_content')
                    ->title('Ответное письмо')
                    ->toolbar(["text", "color", "header", "list", "format"])
                    ->canSee($this->feedback->exists && $this->feedback->user),
            ]),
        ];
    }

    public function save(Feedback $feedback, Request $request)
    {
        $feedback->fill($request->get('feedback'))->save();
        
        Alert::info('Обращение успешно обновлено.');
        
        return redirect()->route('platform.feedback.list');
    }

    public function sendResponse(Feedback $feedback, Request $request)
    {
        $request->validate([
            'response_content' => 'required|string',
        ]);

        $user = $feedback->user;
        
        Mail::to($user->email)->send(new FeedbackResponseMail(
            $feedback->subject,
            $request->input('response_content'),
            $feedback->message
        ));

        Alert::info('Ответ успешно отправлен пользователю.');
        
        return back();
    }
}