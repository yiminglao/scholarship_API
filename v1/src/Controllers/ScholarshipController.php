<?php
/**
 * Created by PhpStorm.
 * User: kyler_000
 * Date: 11/1/2017
 * Time: 8:42 AM
 */

namespace Scholarship\Controllers;

use Scholarship\Http\StatusCodes;
use Scholarship\Models\Question;
use Scholarship\Models\Scholarship;
use Scholarship\Models\Token;

class ScholarshipController
{
    public function update($id, $scholarshipData): array {
        if (Token::getRoleFromToken() !== 'Faculty') {
            http_response_code(StatusCodes::UNAUTHORIZED);
            return [
                'success' => false,
                'error' => 'This action is not allowed'
            ];
        }

        $scholarship = new Scholarship($id);

        foreach ($scholarshipData as $scholarshipField => $value) {
            $scholarship->set($scholarshipField, $value);
        }

        if (property_exists($scholarshipData, 'questions')) {
            $questions = [];
            foreach ($scholarshipData->questions as $questionString) {
                $question = new Question();
                $question->setQuestion($questionString);
                array_push($questions, $question);
            }

            $scholarship->setQuestions($questions);
        }

        try {
            return [
                'success' => $scholarship->save()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'errors' => $e->getMessage()
            ];
        }
    }

    public function create($scholarshipData): array {
        if (Token::getRoleFromToken() !== 'Faculty') {
            http_response_code(StatusCodes::UNAUTHORIZED);
            return [
                'success' => false,
                'error' => 'This action is not allowed'
            ];
        }

        $scholarship = new Scholarship();

        foreach ($scholarshipData as $scholarshipField => $value) {
            $scholarship->set($scholarshipField, $value);
        }

        $questions = [];
        foreach ($scholarshipData->questions as $questionString) {
            $question = new Question();
            $question->setQuestion($questionString);
            array_push($questions, $question);
        }

        $scholarship->setQuestions($questions);

        try {
            return [
                'success' => $scholarship->save()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'errors' => $e->getMessage()
            ];
        }
    }


    public function delete($scholarshipId): array {
        if (Token::getRoleFromToken() !== 'Faculty') {
            http_response_code(StatusCodes::UNAUTHORIZED);
            return [
                'success' => false,
                'error' => 'This action is not allowed'
            ];
        }
        $scholarship = new Scholarship($scholarshipId);

        try {
            return [
                'success' => $scholarship->delete()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'errors' => $e->getMessage()
            ];
        }
    }

    public function getScholarshipById($id)
    {
       // $id = implode("", $id);
        return (new Scholarship($id));
    }

    public function getAllScholarships($args)
    {
        $scholarships = (new Scholarship())->getAllScholarships();
        $encodedArray = array();
        foreach ($scholarships as $scholarship) {
            array_push($encodedArray, $scholarship);
        }

        return $encodedArray;

    }

}