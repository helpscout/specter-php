module Overcommit::Hook::PreCommit
  # Runs `php -l` against any modified PHP files.
  class PhpLint < Base
    # Sample String
    #   PHP Parse error:  syntax error, unexpected 'require_once' (T_REQUIRE_ONCE) in site/sumo.php on line 12
    MESSAGE_REGEX = /^(?<type>.+)\:\s+(?<message>.+) in (?<file>.+) on line (?<line>\d+)/

    def run
      # A list of error messages
      messages = []

      # Exit status for all of the runs. Should be zero!
      exitStatusSum = 0;


      # Run for each of our applicable files
      applicable_files.each do |file|
        result = execute(command, args: [file])
        output = result.stdout.chomp
        exitStatusSum += result.status
        if result.status
          # `php -l` returns with a leading newline, and we only need the first
          # line, there is usually some redundancy
          messages << output.lstrip.split("\n").first
        end
      end

      # If the sum of all lint status is zero, then none had exit status
      return :pass if exitStatusSum == 0

      # No messages is great news for us
      return :pass if messages.empty?

      # Return the list of message objects
      extract_messages(
        messages,
        MESSAGE_REGEX
      )
    end
  end
end