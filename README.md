
## INTRO

I have timed myself to do what I can refactor within 2 hours only.

## BookingController:

GOOD OBSERVATION

- Using dependency injections, this will make the dependency easier to mock during unit test, and makes the class more flexible.


REFACTORING CONSIDERATIONS

- Remove unnecessary line of codes, one example is the `use DTApi\Http\Requests;`
- Make the function and variable names more descriptive, this is to make the code more readable, and maintainable in the long run. e.g. $this->repository could be $this->bookingRepository.
- You may assign long if statements conditions and deep object properties to variables for readability.
- Add proper request validations, and error handling with descriptive message.
- Put all queries into the Repository or Model instead, keep the controller slim but with complete overview of the logic.
- Access config instead of env, this way you can utilize the config cache.
- Be consistent with variable naming convention, sample would be 'jobid' it should follow the camel case format which is 'job_id'.
- Just a suggestion: Add naming convention to boolean type variables to be prefixed by 'is_', sample 'is_by_admin', this way you can easily determine that this variable contains boolean or condition.
- Be consistent on putting comments to methods, put also brief descriptions of the method in the comment.
- Just a suggestion: Utilize the new PHP feature, type hinting. This way you can set the expected return type of each methods. I did not apply it to this refactor.
- Observe DRY principle, retain atleast one of the method acceptJob and acceptJobWithID, it does the same thing.
- If given more context, I might suggest to divide this controller to multiple controllers to maintain single responsibility.


## BookingRepository

GOOD OBSERVATION

- Using dependency injections.

REFACTORING CONSIDERATIONS

- Inconsistent dependency injections.
- Consider putting hardcoded values into a config.
- Consider using interface for injected dependencies, this way its easier to switch to different class later without breaking the current class.
- Consider long codes, like the one with chaining methods to be formatted with new line. You may also use auto formatter tools in you IDE e.g. PHP prettier.
- Delete unecessary comments.
- Be consistent on putting comments to methods, put also brief descriptions of the method in the comment.