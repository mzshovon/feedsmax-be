<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BucketQuestionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('bucket_questions')->truncate();

        $data = [];
        
        // Define available questions (1-20, where 12-17 are rating questions)
        $regularQuestions = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 18, 19, 20];
        $ratingQuestions = [12, 13, 14, 15, 16, 17];
        
        // Track used questions per bucket to avoid duplicates
        $bucketQuestions = [
            1 => [],
            2 => [],
            3 => [],
            4 => []
        ];
        
        $insertedRows = 0;
        
        // For each bucket, assign 4-5 questions (total will be ~18-20 rows)
        for ($bucketId = 1; $bucketId <= 4; $bucketId++) {
            // Determine number of questions for this bucket (4 or 5)
            $questionsPerBucket = ($bucketId <= 2) ? 5 : 5; // This gives us exactly 20 rows
            if ($bucketId == 4) {
                $questionsPerBucket = 20 - $insertedRows; // Ensure we hit exactly 20 rows
            }
            
            // Pick one rating question (12-17) for this bucket
            $selectedRatingQuestion = $ratingQuestions[array_rand($ratingQuestions)];
            $bucketQuestions[$bucketId][] = $selectedRatingQuestion;
            
            // Pick remaining questions from regular questions
            $availableRegular = array_diff($regularQuestions, $bucketQuestions[$bucketId]);
            $remainingQuestionsNeeded = $questionsPerBucket - 1;
            
            // Randomly select remaining questions
            $selectedRegular = array_rand(array_flip($availableRegular), min($remainingQuestionsNeeded, count($availableRegular)));
            if (!is_array($selectedRegular)) {
                $selectedRegular = [$selectedRegular];
            }
            
            $bucketQuestions[$bucketId] = array_merge($bucketQuestions[$bucketId], $selectedRegular);
            
            // Create records for this bucket
            foreach ($bucketQuestions[$bucketId] as $questionId) {
                if ($insertedRows < 20) {
                    $data[] = [
                        'bucket_id' => $bucketId,
                        'question_id' => $questionId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $insertedRows++;
                }
            }
        }
        
        // If we need more rows to reach exactly 20, add some additional ones
        while (count($data) < 20) {
            $bucketId = rand(1, 4);
            $availableQuestions = array_diff(range(1, 20), array_column(array_filter($data, function($item) use ($bucketId) {
                return $item['bucket_id'] == $bucketId;
            }), 'question_id'));
            
            if (!empty($availableQuestions)) {
                $questionId = $availableQuestions[array_rand($availableQuestions)];
                $data[] = [
                    'bucket_id' => $bucketId,
                    'question_id' => $questionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        
        // Trim to exactly 20 rows if we have more
        $data = array_slice($data, 0, 20);
        
        // Insert the data
        DB::table('bucket_questions')->insert($data);
        
        $this->command->info('Seeded 20 bucket questions successfully!');
        $this->command->info('Distribution:');
        
        // Show distribution
        for ($i = 1; $i <= 4; $i++) {
            $count = count(array_filter($data, function($item) use ($i) {
                return $item['bucket_id'] == $i;
            }));
            
            $questions = array_column(array_filter($data, function($item) use ($i) {
                return $item['bucket_id'] == $i;
            }), 'question_id');
            
            $ratingInBucket = array_intersect($questions, [12, 13, 14, 15, 16, 17]);
            
            $this->command->info("Bucket {$i}: {$count} questions - Questions: [" . implode(', ', $questions) . "] - Rating questions: [" . implode(', ', $ratingInBucket) . "]");
        }
    }
}